<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$items = $conn->query("SELECT id, name FROM menu_items ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $offer_name = $conn->real_escape_string($_POST['offer_name']);
    $discount = floatval($_POST['discount']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO offers (item_id, offer_name, discount_percent, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $item_id, $offer_name, $discount, $start, $end);
    
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Offer added successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to add offer.";
    }
    
    $stmt->close();
    header("Location: offers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Offer</title>
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
        body { margin: 0; font-family: 'Poppins', Arial, sans-serif; background: var(--main-bg); color: var(--text-dark); }
        .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100%; background: var(--sidebar-bg); color: var(--text-light); display: flex; flex-direction: column; box-shadow: 4px 0 20px rgba(0,0,0,0.1); }
        .sidebar-header { padding: 25px 20px; text-align: center; font-size: 1.6em; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { color: var(--text-light); text-decoration: none; padding: 16px 25px; display: flex; align-items: center; gap: 15px; font-weight: 500; border-left: 4px solid transparent; transition: .3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(0,0,0,0.2); border-left-color: var(--text-light); }
        .sidebar a.logout { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); }
        .main { margin-left: 240px; padding: 35px; }
        .form-container { max-width: 700px; margin: 0 auto; background: var(--card-bg); padding: 30px 35px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 5px 15px var(--shadow-color); }
        h2 { margin: 0 0 25px; font-size: 1.5em; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); }
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);
            background: #f9fafb; font-size: 1em; transition: .3s;
        }
        input:focus, select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(230,126,34,0.2);
            background:#fff;
        }
        .form-buttons { display: flex; gap: 15px; margin-top: 30px; }
        button, .back-link {
            padding: 12px 25px; border: none; border-radius: 8px;
            text-decoration: none; font-size: 1em; font-weight: 500;
            cursor: pointer; transition: .3s;
        }
        button[type="submit"] { background: var(--primary-accent); color: white; flex-grow: 1; }
        button[type="submit"]:hover { background: var(--primary-accent-dark); transform: translateY(-2px); }
        .back-link { background: var(--border-color); color: var(--text-dark); }
        .back-link:hover { background: #dcdcdc; }
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
            <h2>Add New Offer</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="offer_name">Offer Name</label>
                    <input type="text" id="offer_name" name="offer_name" required>
                </div>

                <div class="form-group">
                    <label for="item_id">Select Item for Offer</label>
                    <select id="item_id" name="item_id" required>
                        <option value="0">-- General Offer (No specific item) --</option>
                        <?php if ($items && $items->num_rows > 0): ?>
                            <?php while($row = $items->fetch_assoc()): ?>
                                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="discount">Discount (%)</label>
                    <input type="number" id="discount" name="discount" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>

                <div class="form-buttons">
                    <a href="offers.php" class="back-link">Cancel</a>
                    <button type="submit">Add Offer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
