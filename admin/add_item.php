<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $image_name = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image_name;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_name = "";
            $_SESSION['error_msg'] = "Sorry, there was an error uploading your file.";
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (name, category, price, rating, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $category, $price, $rating, $image_name);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Item added successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to add item! Error: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: menu.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --sidebar-bg: #d35400;
            --main-bg: #f9fafb;
            --card-bg: #ffffff;
            --primary-accent: #e67e22;
            --primary-accent-dark: #d35400;
            --text-dark: #374151;
            --text-light: #ffffff;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.05);
        }

        body { 
            margin: 0; 
            font-family: 'Poppins', Arial, sans-serif; 
            background-color: var(--main-bg); 
            color: var(--text-dark); 
        }

        .sidebar { 
            position: fixed; top: 0; left: 0; width: 240px; height: 100%; 
            background: var(--sidebar-bg); color: var(--text-light); 
            display: flex; flex-direction: column; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.1); 
            z-index: 100; 
        }
        .sidebar-header { 
            padding: 25px 20px; text-align: center; 
            font-size: 1.6em; font-weight: 600; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        }
        .sidebar a { 
            color: var(--text-light); text-decoration: none; 
            padding: 16px 25px; 
            display: flex; align-items: center; gap: 15px; 
            font-weight: 500; border-left: 4px solid transparent; 
            transition: all 0.3s ease; 
        }
        .sidebar a:hover, .sidebar a.active { 
            background: rgba(0, 0, 0, 0.2); 
            border-left-color: var(--text-light); 
        }
        .sidebar a.logout { 
            margin-top: auto; 
            border-top: 1px solid rgba(255, 255, 255, 0.1); 
        }

        .main { 
            margin-left: 240px; 
            padding: 35px; 
        }
        
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 30px 35px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        h2 {
            text-align: left;
            color: var(--text-dark);
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.5em;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-muted);
            font-size: 0.9em;
        }

        input[type="text"], 
        input[type="number"], 
        input[type="file"],
        select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-sizing: border-box;
            transition: all 0.3s;
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
            font-size: 1em;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2);
            background-color: #fff;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        button, .back-link {
            text-decoration: none;
            text-align: center;
            padding: 12px 25px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        button[type="submit"] {
            background: var(--primary-accent);
            color: white;
            flex-grow: 1;
        }
        button[type="submit"]:hover {
            background: var(--primary-accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .back-link {
            background-color: var(--border-color);
            color: var(--text-dark);
        }
        .back-link:hover {
            background-color: #dcdcdc;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="manage_orders.php"><i class="fa-solid fa-box"></i> Manage Orders</a>
            <a href="menu.php" class="active"><i class="fa-solid fa-utensils"></i> Manage Menu</a>
            <a href="offers.php"><i class="fa-solid fa-tags"></i> Manage Offers</a>
            <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="admin_con.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>Add New Menu Item</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="rating">Rating (0 to 5)</label>
                    <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" required>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <div class="form-buttons">
                    <a href="menu.php" class="back-link">Cancel</a>
                    <button type="submit">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>