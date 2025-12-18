<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$item = null;
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $rating = floatval($_POST['rating']);
    
    $result = $conn->query("SELECT image FROM menu_items WHERE id=$id");
    $currentItem = $result->fetch_assoc();
    $image_name = $currentItem['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        
        $new_image_name = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $new_image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {

            $oldImageFilename = basename($image_name);
            if (!empty($oldImageFilename) && file_exists($targetDir . $oldImageFilename)) {
                unlink($targetDir . $oldImageFilename);
            }
            $image_name = $new_image_name;
        } else {
            $error_message = "Image upload failed!";
        }
    }
    
    if (empty($error_message)) {
        $stmt = $conn->prepare("UPDATE menu_items SET name=?, price=?, category=?, rating=?, image=? WHERE id=?");
        $stmt->bind_param("sdsdsi", $name, $price, $category, $rating, $image_name, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Item updated successfully!";
            header("Location: menu.php");
            exit;
        } else {
            $error_message = "Error updating item: " . $conn->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM menu_items WHERE id=$id");
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        $error_message = "Item not found.";
    }
} else {
    $error_message = "No item ID provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        :root {
            --sidebar-bg: #d35400; --main-bg: #f9fafb; --card-bg: #ffffff;
            --primary-accent: #e67e22; --primary-accent-dark: #d35400;
            --text-dark: #374151; --text-light: #ffffff; --text-muted: #6b7280;
            --border-color: #e5e7eb; --shadow-color: rgba(0, 0, 0, 0.05);
            --error-bg: #fee2e2; --error-text: #b91c1c;
        }
        body { margin: 0; font-family: 'Poppins', Arial, sans-serif; background-color: var(--main-bg); color: var(--text-dark); }
        .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100%; background: var(--sidebar-bg); color: var(--text-light); display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.1); z-index: 100; }
        .sidebar-header { padding: 25px 20px; text-align: center; font-size: 1.6em; font-weight: 600; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar a { color: var(--text-light); text-decoration: none; padding: 16px 25px; display: flex; align-items: center; gap: 15px; font-weight: 500; border-left: 4px solid transparent; transition: all 0.3s ease; }
        .sidebar a:hover, .sidebar a.active { background: rgba(0, 0, 0, 0.2); border-left-color: var(--text-light); }
        .sidebar a.logout { margin-top: auto; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .main { margin-left: 240px; padding: 35px; }
        .form-container { max-width: 700px; margin: 0 auto; background: var(--card-bg); padding: 30px 35px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 5px 15px var(--shadow-color); }
        h2 { text-align: left; color: var(--text-dark); font-weight: 600; margin-top: 0; margin-bottom: 25px; font-size: 1.5em; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); font-size: 0.9em; }
        input[type="text"], input[type="number"], input[type="file"], select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); box-sizing: border-box; transition: all 0.3s; background-color: #f9fafb; font-family: 'Poppins', sans-serif; font-size: 1em; }
        input:focus, select:focus { outline: none; border-color: var(--primary-accent); box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2); background-color: #fff; }
        .current-image-wrapper { text-align: center; margin-bottom: 15px; }
        .current-image { max-width: 150px; border-radius: 8px; border: 2px solid var(--border-color); }
        .form-buttons { display: flex; gap: 15px; margin-top: 30px; }
        button, .back-link { text-decoration: none; text-align: center; padding: 12px 25px; border: none; cursor: pointer; border-radius: 8px; font-size: 1em; font-weight: 500; transition: all 0.3s ease; }
        button[type="submit"] { background: var(--primary-accent); color: white; flex-grow: 1; }
        button[type="submit"]:hover { background: var(--primary-accent-dark); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .back-link { background-color: var(--border-color); color: var(--text-dark); }
        .back-link:hover { background-color: #dcdcdc; }
        .error { background-color: var(--error-bg); color: var(--error-text); padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
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
            <h2>Edit Menu Item</h2>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?= $error_message ?></p>
            <?php endif; ?>
            
            <?php if ($item): ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $item['id']; ?>">
                
                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($item['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= htmlspecialchars($item['category']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" step="0.01" name="price" value="<?= $item['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="rating">Rating (0 to 5)</label>
                    <input type="number" id="rating" step="0.1" name="rating" value="<?= $item['rating']; ?>" min="0" max="5" required>
                </div>

                <?php if (!empty($item['image']) && file_exists(__DIR__ . "/uploads/" . basename($item['image']))): ?>
                    <div class="form-group">
                        <label>Current Image</label>
                        <div class="current-image-wrapper">
                            <img src="uploads/<?= basename($item['image']); ?>" alt="Item Image" class="current-image">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="image">Change Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                
                <div class="form-buttons">
                    <a href="menu.php" class="back-link">Cancel</a>
                    <button type="submit">Update Item</button>
                </div>
            </form>
            <?php else: ?>
                <div class="form-buttons">
                    <a href="menu.php" class="back-link">← Back to Manage Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>