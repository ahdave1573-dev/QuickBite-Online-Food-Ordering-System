<?php
session_start();
if (!isset($_SESSION['admin'])) { 
    header("Location: admin_login.php"); 
    exit; 
}

include "db_connect.php";

$sql = "SELECT o.*, m.name as item_name 
        FROM offers o 
        JOIN menu_items m ON o.item_id = m.id
        ORDER BY o.start_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Offers</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { width: 90%; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: center; }
        th { background: #e74c3c; color: white; }
        a { text-decoration: none; padding: 5px 10px; border-radius: 5px; }
        .add-btn { background: #2ecc71; color: white; display: inline-block; margin: 10px 0; }
        .edit-btn { background: #3498db; color: white; }
        .delete-btn { background: #e74c3c; color: white; }
        img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Offers List</h2>
    <a href="add_offer.php" class="add-btn">+ Add New Offer</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Offer Name</th>
            <th>Discount (%)</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= htmlspecialchars($row['offer_name']) ?></td>
            <td><?= $row['discount_percent'] ?> %</td>
            <td><?= $row['start_date'] ?></td>
            <td><?= $row['end_date'] ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <img src="../uploads/offers/<?= htmlspecialchars($row['image']) ?>" alt="Offer Image">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td>
                <a href="edit_offer.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                <a href="delete_offer.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this offer?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
