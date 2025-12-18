<?php
/*
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Get item ID from URL and fetch item details from DATABASE (This is SECURE)
$item_id = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);
if (!$item_id) {
    die("Invalid item selected.");
}

$stmt = $conn->prepare("SELECT name, price FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Item not found.");
}
$item = $result->fetch_assoc();
$item_name = $item['name'];
$item_price = $item['price']; // Authoritative price from the database
$stmt->close();

// Variables for showing messages
$message = '';
$message_type = '';
$order_placed = false;

// 3. Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if (!empty($name) && !empty($phone) && $quantity > 0) {
        $total = $item_price * $quantity;

        // Insert into database
        $insert_stmt = $conn->prepare("INSERT INTO orders (username, customer_name, customer_phone, item_name, item_price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssdid", $_SESSION['username'], $name, $phone, $item_name, $item_price, $quantity, $total);

        if ($insert_stmt->execute()) {
            $message = "Thank you, " . htmlspecialchars($name) . "! Your order has been placed successfully.";
            $message_type = 'success';
            $order_placed = true;
        } else {
            $message = "Failed to place order. Please try again.";
            $message_type = 'error';
        }
        $insert_stmt->close();
    } else {
        $message = "Please fill all fields correctly.";
        $message_type = 'error';
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order <?= htmlspecialchars($item_name); ?> | QuickBite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff9f0; margin: 0; }
        .header { background-color: #ff5722; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .header .logo { font-size: 1.8em; font-weight: bold; }
        .header nav a { color: white; margin: 0 15px; font-weight: bold; text-decoration: none; }
        .container { max-width: 600px; margin: 40px auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .item-details { text-align: center; font-size: 1.2em; color: #ff5722; font-weight: bold; margin-bottom: 25px; }

    
        form label { font-weight: bold; margin-bottom: 5px; display: block; }
        form input[type="text"], form input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        form button {
            width: 100%;
            padding: 12px;
            background-color: #43a047;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        form button:hover { background-color: #388e3c; }
        
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.1em;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        
        .footer { background-color: #333; color: #eee; text-align: center; padding: 20px 0; margin-top: 40px; }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">QuickBite</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <h2>Place Your Order</h2>
        <p class="item-details"><?= htmlspecialchars($item_name); ?> (₹<?= htmlspecialchars(number_format($item_price, 2)); ?>)</p>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type; ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$order_placed): ?>
            <form method="POST" action="order.php?item_id=<?= $item_id; ?>">
                <div>
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div>
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your 10-digit phone number" required>
                </div>
                <div>
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                </div>
                <button type="submit">Place Order Now</button>
            </form>
        <?php else: ?>
            <a href="menu.php" style="display:inline-block; width: calc(100% - 24px); padding:12px; text-align:center; background-color:#ff5722; color:white; text-decoration:none; border-radius:5px;">← Back to Menu</a>
        <?php endif; ?>
    </main>
    

    <footer class="footer">
        &copy; <?= date("Y") ?> QuickBite. All rights reserved.
    </footer>
</body>
</html>
*/