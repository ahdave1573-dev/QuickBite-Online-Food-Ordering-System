<?php
session_start();

$item = $_GET['item'] ?? 'Unknown';
$price = $_GET['price'] ?? 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$item])) {
    $_SESSION['cart'][$item]['quantity'] += 1;
} else {
    $_SESSION['cart'][$item] = ['price' => $price, 'quantity' => 1];
}

header("refresh:2;url=cart.php");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Added to Cart</title>
</head>
<body style="background-color: #fff9f0; font-family: Arial, sans-serif; text-align: center; padding: 50px;">

  <div style="display: inline-block; padding: 30px; background-color: #e0f7fa; border: 2px solid #4caf50; border-radius: 10px;">
    <h2 style="color: #4caf50;">✅ Item Added to Cart</h2>
    <p><strong><?php echo htmlspecialchars($item); ?></strong> added successfully!</p>
    <p>Redirecting to cart...</p>
  </div>

</body>
</html>
