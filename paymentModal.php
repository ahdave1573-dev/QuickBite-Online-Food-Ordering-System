<?php

require_once 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['order'])) {
    echo "<script>alert('⚠️ No order data found or invalid request!'); window.history.back();</script>";
    exit;
}

$username = $_SESSION['username'];
$order = $_POST['order'];
$sub_total = floatval($_POST['grand_total'] ?? 0);
$discount_amount = floatval($_POST['discount_amount'] ?? 0);
$offer_name = htmlspecialchars($_POST['offer_name'] ?? ''); 
$offer_percentage = floatval($_POST['offer_percentage'] ?? 0); 
$grand_total = $sub_total - $discount_amount;

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "food";
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->begin_transaction();

$order_successful = true;
try {
    $stmt = $conn->prepare("INSERT INTO orders (username, item, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($order as $details) {
        $stmt->bind_param("ssdid", $username, $details['name'], $details['price'], $details['quantity'], $details['price']*$details['quantity']);
        $stmt->execute();
    }
    $conn->commit();
} catch (Exception $e) {
    $order_successful = false;
    $conn->rollback();
} finally {
    if (isset($stmt)) { $stmt->close(); }
    $conn->close();
}

if ($order_successful) {
    unset($_SESSION['cart']);
}
?>

<style>

    .container { max-width: 800px; margin: 40px auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
    .success { color: #43a047; }
    .error { color: #d32f2f; }
    .order-table { width: 100%; border-collapse: collapse; margin: 25px 0; }
    .order-table th, .order-table td { padding: 12px; border: 1px solid #eee; text-align: left; }
    .order-table th { background-color: #f5f5f5; font-weight: bold; }
    .order-table .discount td { color: #43a047; font-weight: bold; }
    .order-table .grand-total { font-weight: bold; font-size: 1.2em; background-color: #f0f0f0; }
    .order-table td:nth-child(2), .order-table td:nth-child(3), .order-table td:nth-child(4) { text-align: center; }
    .action-buttons { margin-top: 30px; display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; }
    .btn { display: inline-block; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1em; border: none; cursor: pointer; transition: opacity 0.3s; }
    .btn.shopping { background-color: #ff5722; color: white; }
    .btn.print { background-color: #0288d1; color: white; }
    .btn.payment { background-color: #43a047; color: white; }

    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 550px; border-radius: 10px; text-align: left; position: relative; animation: fadeIn 0.3s; }
    @keyframes fadeIn { from {opacity: 0; transform: translateY(-20px);} to {opacity: 1; transform: translateY(0);} }
    .close-btn { color: #aaa; position: absolute; top: 10px; right: 20px; font-size: 28px; font-weight: bold; cursor: pointer; }
    .modal-details { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    
    .payment-options { margin-top: 20px; }
    .payment-option { display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 12px; cursor: pointer; transition: border-color 0.3s, box-shadow 0.3s; }
    .payment-option:hover { border-color: #aaa; }
    .payment-option.selected { border-color: #43a047; box-shadow: 0 0 8px rgba(67, 160, 71, 0.5); }
    .payment-option input[type="radio"] { display: none; } /* Hide the default radio button */
    .payment-option .icon { font-size: 2em; margin-right: 15px; width: 40px; text-align: center;}
    .payment-option .details { display: flex; flex-direction: column; }
    .payment-option .details span { font-weight: bold; }
    .payment-option .details small { color: #777; }

    #confirmPaymentBtn { width: 100%; padding: 15px; margin-top: 20px; font-size: 1.1em; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
    #confirmPaymentBtn:hover { opacity: 0.9; }


</style>

<div class="container">
    <?php if ($order_successful): ?>
        <h1 class="success">✅ Order Placed Successfully!</h1>
        <p>Thank you, <strong><?= htmlspecialchars($username) ?></strong>. Your order has been confirmed.</p>
        
        <table class="order-table"> </table>

        <div class="action-buttons">
            <a href="menu.php" class="btn shopping">← Continue Shopping</a>
            <button id="printBtn" class="btn print">🖨️ Print Bill</button>
            <button id="paymentBtn" class="btn payment">Proceed to Payment</button>
        </div>
    <?php else: ?>
        <?php endif; ?>
</div>


<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Select Payment Method</h2>
        
        <div id="modalOrderDetails" class="modal-details">
            </div>
        
        <p style="text-align:center; font-size: 1.2em;"><strong>Grand Total: ₹<?= number_format($grand_total, 2) ?></strong></p>
        
        <div class="payment-options">
            <label class="payment-option" data-method="upi">
                <input type="radio" name="payment_method" value="upi" checked>
                <div class="icon">💳</div>
                <div class="details">
                    <span>UPI / Google Pay / PhonePe</span>
                    <small>Pay using any UPI app.</small>
                </div>
            </label>

            <label class="payment-option" data-method="card">
                <input type="radio" name="payment_method" value="card">
                <div class="icon">Credit Card</div>
                <div class="details">
                    <span>Credit / Debit Card</span>
                    <small>Visa, Mastercard, Rupay, and more.</small>
                </div>
            </label>

            <label class="payment-option" data-method="cod">
                <input type="radio" name="payment_method" value="cod">
                <div class="icon">💵</div>
                <div class="details">
                    <span>Cash on Delivery</span>
                    <small>Pay with cash upon delivery.</small>
                </div>
            </label>
        </div>

        <button id="confirmPaymentBtn">Pay ₹<?= number_format($grand_total, 2) ?></button>
    </div>
</div>