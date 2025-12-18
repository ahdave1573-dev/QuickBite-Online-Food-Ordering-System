<?php

session_start();

if (isset($_POST['action']) && $_POST['action'] === 'update_payment') {
    header('Content-Type: application/json'); 
    if (!isset($_POST['order_id'], $_POST['payment_method'], $_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }
    $order_id = intval($_POST['order_id']);
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $username = $_SESSION['username'];
    $conn = new mysqli("localhost", "root", "", "food");
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE orders SET payment_method = ?, order_status = 'Processing' WHERE id = ? AND username = ?");
    if ($stmt) {
        $stmt->bind_param("sis", $payment_method, $order_id, $username);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Payment method confirmed!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
    $conn->close();
    exit;
}

require_once 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['order'])) {
    echo "<script>alert('⚠️ No order data found or invalid request!'); window.history.back();</script>";
    exit;
}

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$order_items_from_post = $_POST['order'];
$order_successful = false;
$new_order_id = null;
$recalculated_sub_total = 0;
$final_order_items = [];

$conn->begin_transaction();
try {
    $stmt_verify = $conn->prepare("SELECT price FROM menu_items WHERE name = ?");
    if (!$stmt_verify) throw new Exception("Failed to prepare verification statement.");

    foreach ($order_items_from_post as $item) {
        $itemName = $item['name'];
        $quantity = intval($item['quantity']);
        if ($quantity <= 0) continue;

        $stmt_verify->bind_param("s", $itemName);
        $stmt_verify->execute();
        $result = $stmt_verify->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $db_original_price = floatval($row['price']);
            $recalculated_sub_total += $db_original_price * $quantity;
            $display_price = floatval($item['price']);
            $final_order_items[] = [
                'name' => $itemName,
                'price' => $display_price,
                'quantity' => $quantity
            ];
        } else {
            throw new Exception("Item '{$itemName}' not found in the menu table.");
        }
    }
    $stmt_verify->close();

    if (empty($final_order_items)) {
        throw new Exception("Cannot create an order with no valid items.");
    }

    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $offer_name = htmlspecialchars($_POST['offer_name'] ?? '');
    $offer_percentage = floatval($_POST['offer_percentage'] ?? 0);
    $grand_total = $recalculated_sub_total - $discount_amount;

    $stmt_order = $conn->prepare("INSERT INTO orders (username, grand_total, discount_amount, offer_name) VALUES (?, ?, ?, ?)");
    if (!$stmt_order) throw new Exception("Prepare failed for 'orders' table: " . $conn->error);
    $stmt_order->bind_param("sdds", $username, $grand_total, $discount_amount, $offer_name);
    $stmt_order->execute();
    $new_order_id = $conn->insert_id;
    $stmt_order->close();

    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)");
    if (!$stmt_items) throw new Exception("Prepare failed for 'order_items' table: " . $conn->error);
    foreach ($final_order_items as $item) {
        $stmt_items->bind_param("isdi", $new_order_id, $item['name'], $item['price'], $item['quantity']);
        $stmt_items->execute();
    }
    $stmt_items->close();

    $conn->commit();
    $order_successful = true;

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
} finally {
    $conn->close();
}

if ($order_successful) {
    unset($_SESSION['cart']);
}

$sub_total = $recalculated_sub_total;
$order = $final_order_items;
?>

<style>

    .container { max-width: 800px; margin: 40px auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
    .success { color: #43a047; }
    .error { color: #d32f2f; }
    
    .order-table { width: 100%; border-collapse: collapse; margin: 25px 0; }
    .order-table th, .order-table td { padding: 12px; border: 1px solid #eee; text-align: left; vertical-align: middle; }
    .order-table th { background-color: #f5f5f5; font-weight: bold; }
    .order-table .text-right { text-align: right; }
    .order-table .font-bold { font-weight: bold; }
    .order-table .totals-row td { border-top: 2px solid #ccc; }
    .order-table .discount td { color: #43a047; font-weight: bold; }
    .order-table .grand-total { font-weight: bold; font-size: 1.2em; background-color: #f0f0f0; }
    .order-table td:nth-child(2), .order-table td:nth-child(3), .order-table td:nth-child(4) { text-align: center; }
    

    .action-buttons { margin-top: 30px; display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; }
    .btn { display: inline-block; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1em; border: none; cursor: pointer; transition: opacity 0.3s; }
    .btn.shopping { background-color: #ff5722; color: white; }
    .btn.print { background-color: #0288d1; color: white; }
    .btn.payment { background-color: #43a047; color: white; }
    #printBtn { display: none; } /* Print button hidden by default */

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 450px; border-radius: 10px; text-align: left; position: relative; animation: fadeIn 0.3s; }
    @keyframes fadeIn { from {opacity: 0; transform: translateY(-20px);} to {opacity: 1; transform: translateY(0);} }
    .close-btn { color: #aaa; position: absolute; top: 10px; right: 20px; font-size: 28px; font-weight: bold; cursor: pointer; }
    .modal-details { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .payment-option { display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 12px; cursor: pointer; transition: all 0.3s; }
    .payment-option:hover { border-color: #aaa; }
    .payment-option.selected { border-color: #43a047; box-shadow: 0 0 8px rgba(67,160,71,0.5); }
    .payment-option input[type="radio"] { display: none; }
    .payment-option .icon { font-size: 1.8em; margin-right: 15px; width: 40px; text-align: center; }
    .payment-option .details { display: flex; flex-direction: column; }
    .payment-option .details span { font-weight: bold; }
    .payment-option .details small { color: #777; }
    #confirmPaymentBtn { width: 100%; padding: 15px; margin-top: 20px; font-size: 1.1em; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
    #confirmPaymentBtn:disabled { background-color: #aaa; cursor: not-allowed; }
    
    @media print { 
        body * { visibility: hidden; } 
        .container, .container * { visibility: visible; } 
        header, footer, .action-buttons, .modal { display: none; } 
    }
</style>

<div class="container">
    <?php if ($order_successful): ?>
        <h1 class="success">✅ Order Placed Successfully!</h1>
        <p>Thank you, <strong><?= htmlspecialchars($username) ?></strong>. Your order has been confirmed.</p>

        <table class="order-table">
            <thead>
                <tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($order as $details): ?>
                <tr>
                    <td><?= htmlspecialchars($details['name']) ?></td>
                    <td>₹<?= number_format($details['price'], 2) ?></td>
                    <td><?= intval($details['quantity']) ?></td>
                    <td>₹<?= number_format($details['price'] * $details['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="totals-row">
                    <td colspan="3" class="text-right font-bold">Subtotal</td>
                    <td class="font-bold">₹<?= number_format($sub_total, 2) ?></td>
                </tr>
                <?php if ($discount_amount > 0): ?>
                <tr class="discount">
                    <td colspan="3" class="text-right">Discount (<?= $offer_name ?> @ <?= rtrim(rtrim(number_format($offer_percentage, 2), '0'), '.') ?>%)</td>
                    <td>- ₹<?= number_format($discount_amount, 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="grand-total">
                    <td colspan="3" class="text-right">Grand Total</td>
                    <td>₹<?= number_format($grand_total, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="action-buttons">
            <a href="menu.php" class="btn shopping">← Continue Shopping</a>
            <button id="printBtn" class="btn print">🖨️ Print Bill</button>
            <button id="paymentBtn" class="btn payment">Proceed to Payment</button>
        </div>
    <?php else: ?>
        <h1 class="error">❌ Order Failed</h1>
        <p>We're sorry, but there was an error processing your order. Please try again.</p>
        <a href="cart.php" class="btn shopping">← Back to Cart</a>
    <?php endif; ?>
</div>

<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Select Payment Method</h2>
        <div id="modalOrderDetails"></div>
        <p style="text-align:center;font-size:1.2em;"><strong>Grand Total: ₹<?= number_format($grand_total ?? 0, 2) ?></strong></p>
        <div class="payment-options">
            <label class="payment-option" data-method="upi">
                <input type="radio" name="payment_method" value="upi" checked>
                <div class="icon">📱</div>
                <div class="details"><span>UPI / Google Pay</span><small>Pay using any UPI app.</small></div>
            </label>
            <label class="payment-option" data-method="card">
                <input type="radio" name="payment_method" value="card">
                <div class="icon">💳</div>
                <div class="details"><span>Credit / Debit Card</span><small>Visa, Mastercard, Rupay</small></div>
            </label>
            <label class="payment-option" data-method="cod">
                <input type="radio" name="payment_method" value="cod">
                <div class="icon">💵</div>
                <div class="details"><span>Cash on Delivery</span><small>Pay with cash upon delivery.</small></div>
            </label>
        </div>
        <button id="confirmPaymentBtn">Pay ₹<?= number_format($grand_total ?? 0, 2) ?></button>
    </div>
</div>

<?php if ($order_successful): ?>
<script>

    const orderData = <?= json_encode($order) ?>;
    const grandTotal = <?= $grand_total ?>;
    const newOrderId = <?= $new_order_id ?>;
    
    const printBtn = document.getElementById('printBtn');
    const paymentModal = document.getElementById('paymentModal');
    const paymentBtn = document.getElementById('paymentBtn');
    const closeBtn = document.querySelector('.modal .close-btn');
    const paymentOptions = document.querySelectorAll('.payment-option');
    const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');


    printBtn.addEventListener('click', () => window.print());
    paymentBtn.addEventListener('click', () => {
        let detailsHtml = '<p><strong>Your order summary:</strong></p><ul>';
        orderData.forEach(item => {
            detailsHtml += `<li>${item.name} - ₹${parseFloat(item.price).toFixed(2)} x ${parseInt(item.quantity)}</li>`;
        });
        detailsHtml += '</ul>';
        document.getElementById('modalOrderDetails').innerHTML = detailsHtml;
        paymentModal.style.display = 'block';
    });


    const closeModal = () => paymentModal.style.display = 'none';
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target == paymentModal) closeModal();
    });


    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            const selectedMethod = this.dataset.method;
            confirmPaymentBtn.textContent = (selectedMethod === 'cod') 
                ? 'Confirm Order (COD)' 
                : `Pay ₹${grandTotal.toFixed(2)}`;
        });
    });

    document.querySelector('.payment-option[data-method="upi"]').classList.add('selected');

    confirmPaymentBtn.addEventListener('click', function() {
        this.disabled = true;
        this.textContent = 'Processing...';
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        const formData = new FormData();
        formData.append('action', 'update_payment');
        formData.append('order_id', newOrderId);
        formData.append('payment_method', selectedMethod);

        fetch('place_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ Thank you! Your payment with '${selectedMethod.toUpperCase()}' has been confirmed.`);
                paymentBtn.style.display = 'none';
                printBtn.style.display = 'inline-block';
            } else {
                alert('❌ Error: ' + data.message);
                this.disabled = false;
            }
            closeModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ An unexpected error occurred. Please try again.');
            this.disabled = false;
            confirmPaymentBtn.textContent = `Pay ₹${grandTotal.toFixed(2)}`;
        });
    });
</script>
<?php endif; ?>

<?php require_once 'footer.php'; ?>