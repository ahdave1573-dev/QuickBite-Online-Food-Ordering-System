<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$offer = null;
$items = null;
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $item_id = intval($_POST['item_id']);
    $offer_name = $conn->real_escape_string($_POST['offer_name']);
    $discount = floatval($_POST['discount_percent']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE offers SET item_id=?, offer_name=?, discount_percent=?, start_date=?, end_date=? WHERE id=?");
    $stmt->bind_param("isdssi", $item_id, $offer_name, $discount, $start_date, $end_date, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Offer updated successfully!";
        header("Location: offers.php");
        exit;
    } else {
        $message = "Error updating offer: " . $conn->error;
        $error = true;
    }
    $stmt->close();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM offers WHERE id=$id");
    if ($result->num_rows > 0) {
        $offer = $result->fetch_assoc();
    } else {
        $message = "Offer not found.";
        $error = true;
    }
    $items = $conn->query("SELECT id, name FROM menu_items ORDER BY name ASC");
} else {
    $message = "No offer ID provided.";
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Offer</title>
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
        input[type="text"], input[type="number"], input[type="date"], select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); box-sizing: border-box; transition: all 0.3s; background-color: #f9fafb; font-family: 'Poppins', sans-serif; font-size: 1em; }
        input:focus, select:focus { outline: none; border-color: var(--primary-accent); box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2); background-color: #fff; }
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
            <a href="menu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a>
            <a href="offers.php" class="active"><i class="fa-solid fa-tags"></i> Manage Offers</a>
            <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="admin_con.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="form-container">
            <h2>Edit Offer</h2>

            <?php if (!empty($message)): ?>
                <p class="error"><?= $message; ?></p>
            <?php endif; ?>

            <?php if ($offer): ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= $offer['id']; ?>">
                <div class="form-group">
                    <label for="offer_name">Offer Name</label>
                    <input type="text" id="offer_name" name="offer_name" value="<?= htmlspecialchars($offer['offer_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="item_id">Select Item</label>
                    <select id="item_id" name="item_id" required>
                        <option value="0">-- General Offer (No specific item) --</option>
                        <?php if ($items && $items->num_rows > 0): ?>
                            <?php while($row = $items->fetch_assoc()): ?>
                                <option value="<?= $row['id']; ?>" <?= ($row['id'] == $offer['item_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="discount_percent">Discount (%)</label>
                    <input type="number" id="discount_percent" step="0.01" name="discount_percent" value="<?= $offer['discount_percent']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?= $offer['start_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?= $offer['end_date']; ?>" required>
                </div>
                <div class="form-buttons">
                    <a href="offers.php" class="back-link">Cancel</a>
                    <button type="submit">Update Offer</button>
                </div>
            </form>
            <?php else: ?>
                <div class="form-buttons">
                    <a href="offers.php" class="back-link">← Back to Manage Offers</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>