<?php

require_once 'auth_check.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $product_id = $_POST['id'] ?? null;

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    } elseif ($product_id && $action) {
        if ($action === 'add') {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id'               => $product_id,
                    'name'             => $_POST['name'] ?? 'Item',
                    'price'            => floatval($_POST['price'] ?? 0),
                    'image'            => $_POST['image'] ?? 'images/placeholder.png',
                    'quantity'         => 1,
                    'offer_percentage' => floatval($_POST['offer_percentage'] ?? 0),
                    'offer_name'       => htmlspecialchars($_POST['offer_name'] ?? '')
                ];
            }
        } elseif ($action === 'remove') {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']--;
                if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        }
    }
    header("Location: cart.php");
    exit;
}

$page_title = "Shopping Cart - QuickBite";
require_once 'header.php';
?>

<style>
    .container { max-width: 1200px; margin: 40px auto; padding: 20px; }
    .cart-title { text-align: center; font-size: 2.5em; margin-bottom: 30px; color: var(--primary-dark); }
    .cart-table { width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-radius: 12px; overflow: hidden; }
    .cart-table th, .cart-table td { padding: 18px 20px; text-align: left; vertical-align: middle; }
    .cart-table thead { background-color: var(--primary-color); color: white; font-size: 1.1em; }
    .cart-table tbody tr { border-bottom: 1px solid #eee; }
    .cart-table .product-info { display: flex; align-items: center; gap: 15px; }
    .cart-table .product-info img { width: 75px; height: 75px; object-fit: cover; border-radius: 8px; }
    .cart-table .product-info span { font-weight: 500; }
    .quantity-actions { display: flex; align-items: center; }
    .quantity-actions button { background-color: #f0f0f0; border: 1px solid #ccc; border-radius: 50%; cursor: pointer; width: 32px; height: 32px; font-size: 18px; font-weight: bold; transition: background-color 0.3s; }
    .quantity-actions button:hover { background-color: #ddd; }
    .quantity-display { margin: 0 12px; font-size: 1.2em; font-weight: 500; min-width: 30px; text-align: center; }
    .offer-tag { background-color: #e64a19; color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.8em; font-weight: bold; margin-left: 10px; }
    .price-details del { color: #999; font-size: 0.9em; }
    .totals-section td { font-size: 1.1em; }
    .discount-row td { color: #43a047; font-weight: bold; }
    .grand-total-row { font-weight: bold; font-size: 1.4em; background-color: #fff8f0; }
    .cart-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; flex-wrap: wrap; gap: 15px; }
    .cart-actions-left { display: flex; gap: 15px; }
    .action-btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; color: white; font-size: 1em; font-weight: bold; text-decoration: none; transition: all 0.3s; }
    .action-btn:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .continue-shopping-btn { background-color: #6c757d; }
    .clear-cart-btn { background-color: #e64a19; }
    .place-order-btn { background-color: #43a047; }
    .empty-cart { text-align: center; padding: 60px 20px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .empty-cart-text { font-size: 1.5em; color: #555; margin-bottom: 25px; }
</style>

<div class="container">
    <h2 class="cart-title">🛒 Your Shopping Cart</h2>
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <p class="empty-cart-text">⚠️ Your cart is currently empty!</p>
            <a href="menu.php" class="action-btn continue-shopping-btn">← Browse Our Menu</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subTotal = 0;
                $totalDiscount = 0;
                $grandTotal = 0;
                $firstOfferName = '';
                $firstOfferPercentage = 0;

                foreach ($_SESSION['cart'] as $product_id => $details):
                    $original_price_per_item = $details['price'];
                    if (isset($details['offer_percentage']) && $details['offer_percentage'] > 0) {

                        $original_price_per_item = $details['price'] / (1 - ($details['offer_percentage'] / 100));
                    }

                    $itemBaseTotal = $original_price_per_item * $details['quantity'];
                    $itemDiscountAmount = 0;
                    
                    if (isset($details['offer_percentage']) && $details['offer_percentage'] > 0) {
                        $itemDiscountAmount = $itemBaseTotal * ($details['offer_percentage'] / 100);
                        if (empty($firstOfferName)) {
                            $firstOfferName = $details['offer_name'];
                            $firstOfferPercentage = $details['offer_percentage'];
                        }
                    }

                    $subTotal += $itemBaseTotal;
                    $totalDiscount += $itemDiscountAmount;
                ?>
                <tr>
                    <td data-label="Product">
                        <div class="product-info">
                            <img src="<?= htmlspecialchars($details['image']) ?>" alt="<?= htmlspecialchars($details['name']) ?>">
                            <div>
                                <span><?= htmlspecialchars($details['name']) ?></span>
                                <?php if ($itemDiscountAmount > 0): ?>
                                    <span class="offer-tag"><?= $details['offer_percentage'] ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td data-label="Price">
                        <strong>₹<?= number_format($details['price'], 2) ?></strong>
                    </td>
                    <td data-label="Quantity">
                        <div class="quantity-actions">
                            <form method="post"><input type="hidden" name="id" value="<?= $product_id ?>"><button type="submit" name="action" value="remove">−</button></form>
                            <span class="quantity-display"><?= $details['quantity'] ?></span>
                            <form method="post"><input type="hidden" name="id" value="<?= $product_id ?>"><button type="submit" name="action" value="add">+</button></form>
                        </div>
                    </td>
                    <td data-label="Total" style="text-align: right;" class="price-details">
                        <?php if ($itemDiscountAmount > 0): ?>
                            <del>₹<?= number_format($itemBaseTotal, 2) ?></del><br>
                        <?php endif; ?>
                        <strong>₹<?= number_format($details['price'] * $details['quantity'], 2) ?></strong>
                    </td>
                </tr>
                <?php endforeach; 
                
                $grandTotal = $subTotal - $totalDiscount;
                ?>
                
                <tr class="totals-section">
                    <td colspan="3" style="text-align: right;">Subtotal</td>
                    <td data-label="Subtotal" style="text-align: right;">₹<?= number_format($subTotal, 2) ?></td>
                </tr>
                <?php if ($totalDiscount > 0): ?>
                <tr class="totals-section discount-row">
                    <td colspan="3" style="text-align: right;">Discount (<?= htmlspecialchars($firstOfferName) ?>)</td>
                    <td data-label="Discount" style="text-align: right;">- ₹<?= number_format($totalDiscount, 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="grand-total-row">
                    <td colspan="3" style="text-align: right;">Grand Total</td>
                    <td data-label="Grand Total" style="text-align: right;">₹<?= number_format($grandTotal, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="cart-actions">
            <div class="cart-actions-left">
                <a href="menu.php" class="action-btn continue-shopping-btn">← Continue Shopping</a>
                <form method="post"><button type="submit" name="action" value="clear" class="action-btn clear-cart-btn">Clear Cart</button></form>
            </div>
            
            <form method="post" action="place_order.php">
                <input type="hidden" name="grand_total" value="<?= $subTotal ?>">
                <input type="hidden" name="discount_amount" value="<?= $totalDiscount ?>">
                <input type="hidden" name="offer_name" value="<?= htmlspecialchars($firstOfferName) ?>">
                <input type="hidden" name="offer_percentage" value="<?= $firstOfferPercentage ?>">
                
                <?php foreach ($_SESSION['cart'] as $id => $details): ?>
                    <input type="hidden" name="order[<?= $id ?>][name]" value="<?= htmlspecialchars($details['name']) ?>">
                    <input type="hidden" name="order[<?= $id ?>][price]" value="<?= $details['price'] ?>">
                    <input type="hidden" name="order[<?= $id ?>][quantity]" value="<?= $details['quantity'] ?>">
                <?php endforeach; ?>
                <button type="submit" class="action-btn place-order-btn">Place Order →</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'footer.php';
?>