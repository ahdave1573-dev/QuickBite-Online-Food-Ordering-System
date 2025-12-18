<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt_items = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt_items->bind_param("i", $deleteId);
    $stmt_items->execute();
    $stmt_items->close();

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_orders.php?deleted=true");
    exit;
}

if (isset($_GET['status']) && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $newStatus = $_GET['status'];

    $allowed_statuses = ['Pending Order', 'Completed', 'Cancelled'];

    if (in_array($newStatus, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_orders.php?updated=true");
        exit;
    }
}

$sql = "SELECT id, username, grand_total, discount_amount, offer_name, order_status, created_at FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
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
            --pending-color: #f59e0b;
            --completed-color: #10b981;
            --cancelled-color: #ef4444;
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
            white-space: nowrap;
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
        
        .status-badge { 
            padding: 5px 12px; border-radius: 50px; 
            color: white; font-weight: 600; font-size: 0.8em;
            text-transform: capitalize; 
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }

        .status-pending-order { background-color: var(--pending-color) !important; color: #fff !important; }
        .status-pending-payment { background-color: var(--pending-color) !important; color: #fff !important; }
        .status-processing { background-color: var(--pending-color) !important; color: #fff !important; }
        .status-completed { background-color: var(--completed-color) !important; color: #fff !important; }
        .status-cancelled { background-color: var(--cancelled-color) !important; color: #fff !important; }
        
        .actions-cell { text-align: right; }
        .action-btn { 
            text-decoration: none; padding: 7px 12px; border-radius: 6px; 
            color: white; margin: 2px; font-size: 0.85em; font-weight: 500; 
            transition: all 0.2s ease; display: inline-flex; align-items: center; 
            gap: 5px; border: none; cursor: pointer;
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-pending { background-color: var(--pending-color) !important; color: white !important; }
        .btn-completed { background-color: var(--completed-color) !important; color: white !important; }
        .btn-cancelled { background-color: var(--cancelled-color) !important; color: white !important; }
        .btn-delete { background-color: #6b7280 !important; color: white !important; }
        
        .action-btn.active-status { 
            opacity: 0.5; cursor: not-allowed; 
            box-shadow: none; pointer-events: none; 
        }
        .action-btn.active-status:hover { transform: translateY(0); }
        
        .order-items ul { list-style: none; padding-left: 0; margin: 0; white-space: normal; }
        .order-items li { font-size: 0.9em; color: var(--text-dark); padding: 2px 0; }
        .order-items .item-qty { font-size: 0.85em; color: var(--text-muted); }

        .discount-info {
            font-size: 0.85em; color: var(--completed-color); font-weight: 500;
            display: block; margin-top: 4px;
        }
        .total-price strong {
            font-size: 1.05em;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .sidebar { width: 70px; }
            .main { margin-left: 70px; padding: 20px; }
            .sidebar a { padding: 12px; font-size: 0.9em; }
            .main-header h1 { font-size: 1.4em; }
            table th, table td { padding: 10px 8px; font-size: 0.9em; }
            .action-btn { padding: 6px 8px; font-size: 0.8em; }
            .status-badge { min-width: 75px; font-size: 0.75em; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> <span class="link-text">Dashboard</span></a>
            <a href="manage_orders.php" class="active"><i class="fa-solid fa-box"></i> <span class="link-text">Manage Orders</span></a>
            <a href="menu.php"><i class="fa-solid fa-utensils"></i> <span class="link-text">Manage Menu</span></a>
            <a href="offers.php"><i class="fa-solid fa-tags"></i> <span class="link-text">Manage Offers</span></a>
            <a href="manage_users.php"><i class="fa-solid fa-users"></i> <span class="link-text">Manage Users</span></a>
            <a href="admin_con.php"><i class="fa-solid fa-envelope"></i> <span class="link-text">Messages</span></a>
            <a href="admin_logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span class="link-text">Logout</span></a>
        </nav>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>Manage Orders</h1>
        </div>
        
        <div class="content-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Order Details</th>
                        <th>Items</th>
                        <th style="text-align: right;">Total Price</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $row['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="order-items">
                                <?php
                                $order_id = $row['id'];
                                $items_stmt = $conn->prepare("SELECT item_name, quantity, price FROM order_items WHERE order_id = ?");
                                $items_stmt->bind_param("i", $order_id);
                                $items_stmt->execute();
                                $items_result = $items_stmt->get_result();
                                $total_items = 0;
                                echo "<ul>";
                                while($item = $items_result->fetch_assoc()) {
                                    echo "<li>" . htmlspecialchars($item['item_name']) . 
                                         " <span class='item-qty'>(x" . htmlspecialchars($item['quantity']) . ")</span></li>";
                                    $total_items += (int)$item['quantity'];
                                }
                                echo "</ul>";
                                $items_stmt->close();
                                ?>
                            </td>
                            <td><?php echo $total_items; ?></td>
                            <td class="total-price" style="text-align: right;">
                                <strong>₹<?php echo number_format((float)$row['grand_total'], 2); ?></strong>
                                <?php if (!empty($row['discount_amount']) && $row['discount_amount'] > 0): ?>
                                    <small class="discount-info" title="<?php echo htmlspecialchars($row['offer_name']); ?>">
                                        (-₹<?php echo number_format($row['discount_amount'], 2); ?>)
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php
                                $raw_status = isset($row['order_status']) ? $row['order_status'] : 'Pending Order';
                                $status_for_class = strtolower(str_replace(' ', '-', trim($raw_status)));
                                $status_for_class = preg_replace('/[^a-z0-9\-]/', '', $status_for_class);
                                $status_class = 'status-' . $status_for_class;
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($raw_status); ?></span>
                            </td>
                            <td class="actions-cell">
                                <?php $current_status = $raw_status; ?>

                                <a class="action-btn btn-pending <?php if (strcasecmp($current_status, 'Pending Order') == 0) echo 'active-status'; ?>" 
                                   title="Mark as Pending" 
                                   href="manage_orders.php?id=<?php echo $row['id']; ?>&status=<?php echo urlencode('Pending Order'); ?>">
                                   <i class="fa-solid fa-clock"></i>
                                </a>

                                <a class="action-btn btn-completed <?php if (strcasecmp($current_status, 'Completed') == 0) echo 'active-status'; ?>" 
                                   title="Mark as Completed" 
                                   href="manage_orders.php?id=<?php echo $row['id']; ?>&status=<?php echo urlencode('Completed'); ?>">
                                   <i class="fa-solid fa-check"></i>
                                </a>

                                <a class="action-btn btn-cancelled <?php if (strcasecmp($current_status, 'Cancelled') == 0) echo 'active-status'; ?>" 
                                   title="Mark as Cancelled" 
                                   href="manage_orders.php?id=<?php echo $row['id']; ?>&status=<?php echo urlencode('Cancelled'); ?>">
                                   <i class="fa-solid fa-times"></i>
                                </a>

                                <a class="action-btn btn-delete" 
                                   title="Delete Order" 
                                   href="manage_orders.php?delete=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this order and its items?')">
                                   <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align: center; padding: 25px;">No orders found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
