<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT 
            o.id,
            o.offer_name,
            o.discount_percent,
            o.start_date,
            o.end_date,
            m.name AS item_name, 
            m.image AS item_image
        FROM 
            offers o
        LEFT JOIN 
            menu_items m ON o.item_id = m.id
        ORDER BY 
            o.start_date DESC";

$offerResult = $conn->query($sql);

if ($offerResult === false) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Offers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --sidebar-bg: #d35400;
            --main-bg: #f9fafb;
            --card-bg: #ffffff;
            --primary-accent: #e67e22;
            --text-dark: #374151;
            --text-light: #ffffff;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --edit-color: #3b82f6;
            --delete-color: #ef4444;
            --active-color: #10b981;
            --upcoming-color: #8b5cf6;
            --expired-color: #6b7280;
        }

        body { 
            margin: 0; 
            font-family: 'Poppins', Arial, sans-serif; 
            background-color: var(--main-bg); 
            color: var(--text-dark); 
            font-size: 15px;
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
        .main-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 35px; 
        }
        .main-header h1 { 
            font-weight: 600; 
            font-size: 2em; margin: 0; 
        }
        
        .content-card { 
            background: var(--card-bg); 
            padding: 20px 25px; 
            border-radius: 12px; 
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 15px var(--shadow-color); 
            overflow-x: auto;
        }
        
        .content-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 25px; flex-wrap: wrap; gap: 15px; 
        }
        .content-header p { margin: 0; color: var(--text-muted); }
        .add-btn { 
            background: var(--primary-accent); color: white; 
            padding: 10px 20px; border-radius: 8px; 
            text-decoration: none; font-weight: 500; 
            transition: all 0.3s ease; 
            display: inline-flex; align-items: center; gap: 8px; 
            box-shadow: 0 4px 6px rgba(230, 126, 34, 0.2);
        }
        .add-btn:hover { 
            background: #d35400; transform: translateY(-2px); 
            box-shadow: 0 7px 14px rgba(230, 126, 34, 0.2);
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        table th, table td { 
            padding: 14px 12px; 
            text-align: left; 
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        table thead th {
            background-color: var(--main-bg);
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table tbody tr:hover {
            background-color: #f9fafb;
        }
        table tr:last-child td { border-bottom: none; }
        
        .offer-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .offer-details img { 
            width: 50px; height: 50px; 
            object-fit: cover; 
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        .offer-info .offer-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        .offer-info .offer-item {
            font-size: 0.9em;
            color: var(--text-muted);
        }

        .status-badge { 
            padding: 5px 12px; border-radius: 50px; 
            color: white; font-weight: 600; font-size: 0.8em;
            text-transform: capitalize; 
        }
        .status-active { background-color: var(--active-color); }
        .status-upcoming { background-color: var(--upcoming-color); }
        .status-expired { background-color: var(--expired-color); }

        .action-btn { 
            text-decoration: none; padding: 7px 12px; border-radius: 6px; 
            color: white; margin: 2px; font-size: 0.85em; font-weight: 500; 
            transition: all 0.2s ease; display: inline-flex; align-items: center; 
            gap: 5px; border: none; cursor: pointer;
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .edit-btn { background: var(--edit-color); }
        .delete-btn { background: var(--delete-color); }
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
        <div class="main-header">
            <h1>Manage Offers</h1>
        </div>
        
        <div class="content-card">
            <div class="content-header">
                <p>Create, edit, or remove special offers and discounts.</p>
                <a href="add_offer.php" class="add-btn"><i class="fa-solid fa-plus"></i> Add New Offer</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Offer Details</th>
                        <th>Discount</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($offerResult->num_rows > 0): ?>
                        <?php while ($offer = $offerResult->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="offer-details">
                                    <?php if (!empty($offer['item_image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars(basename($offer['item_image'])) ?>" alt="<?= htmlspecialchars($offer['item_name']) ?>">
                                    <?php endif; ?>
                                    <div class="offer-info">
                                        <div class="offer-name"><?= htmlspecialchars($offer['offer_name']) ?></div>
                                        <div class="offer-item"><?= htmlspecialchars($offer['item_name'] ?? 'General Offer') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><strong><?= htmlspecialchars($offer['discount_percent']) ?>%</strong></td>
                            <td>
                                <?= date("d M, Y", strtotime($offer['start_date'])) ?><br>
                                <span class="text-muted">to</span> <?= date("d M, Y", strtotime($offer['end_date'])) ?>
                            </td>
                            <td>
                                <?php
                                $today = date('Y-m-d');
                                $start_date = $offer['start_date'];
                                $end_date = $offer['end_date'];
                                $status_text = '';
                                $status_class = '';

                                if ($today >= $start_date && $today <= $end_date) {
                                    $status_text = 'Active';
                                    $status_class = 'status-active';
                                } elseif ($today > $end_date) {
                                    $status_text = 'Expired';
                                    $status_class = 'status-expired';
                                } else {
                                    $status_text = 'Upcoming';
                                    $status_class = 'status-upcoming';
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                            <td style="text-align: right;">
                                <a href="edit_offer.php?id=<?= $offer['id'] ?>" class="action-btn edit-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="delete_offer.php?id=<?= $offer['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="fa-solid fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 25px;">No offers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>