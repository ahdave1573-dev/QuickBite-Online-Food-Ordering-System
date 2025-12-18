<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$pending_statuses = ['Pending', 'Pending Order', 'Pending Payment'];

$escaped_pending = array_map(function($s) use ($conn) {
    return "'" . $conn->real_escape_string($s) . "'";
}, $pending_statuses);
$pending_in_sql = implode(",", $escaped_pending);

$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'] ?? 0;
$total_pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status IN ($pending_in_sql)")->fetch_assoc()['total'] ?? 0;
$total_menu_items = $conn->query("SELECT COUNT(*) as total FROM menu_items")->fetch_assoc()['total'] ?? 0;
$total_offers = $conn->query("SELECT COUNT(*) as total FROM offers WHERE CURDATE() BETWEEN start_date AND end_date")->fetch_assoc()['total'] ?? 0;
$total_messages = $conn->query("SELECT COUNT(*) as total FROM contact_messages")->fetch_assoc()['total'] ?? 0;
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] ?? 0;

$sql_today_revenue = "SELECT SUM(grand_total) as total FROM orders WHERE DATE(created_at) = CURDATE() AND order_status IN ('Processing', 'Completed')";
$total_today_revenue = $conn->query($sql_today_revenue)->fetch_assoc()['total'] ?? 0;

$sql_month_revenue = "SELECT SUM(grand_total) as total FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND order_status IN ('Processing', 'Completed')";
$total_month_revenue = $conn->query($sql_month_revenue)->fetch_assoc()['total'] ?? 0;

$sql_most_sold = "SELECT oi.item_name, SUM(oi.quantity) as total_sold 
                 FROM order_items oi 
                 JOIN orders o ON oi.order_id = o.id 
                 WHERE o.order_status IN ('Processing', 'Completed') 
                 GROUP BY oi.item_name ORDER BY total_sold DESC LIMIT 5";
$most_sold_items = $conn->query($sql_most_sold);

$sql_recent_orders = "SELECT id, username, grand_total, order_status, created_at FROM orders ORDER BY created_at DESC LIMIT 5";
$recent_orders_result = $conn->query($sql_recent_orders);

$sql_pending_orders = "SELECT id, created_at FROM orders WHERE order_status IN ($pending_in_sql) ORDER BY created_at DESC LIMIT 5";
$pending_orders_result = $conn->query($sql_pending_orders);

$conn->close();

function status_to_class($status) {
    $s = strtolower($status);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return 'status-' . ($s === '' ? 'unknown' : $s);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        :root{
            --sidebar-bg: #d35400;
            --main-bg: #f9f9f9;
            --card-bg: #ffffff;
            --primary-accent: #e67e22;
            --text-dark: #34495e;
            --text-light: #ffffff;
            --text-muted: #95a5a6;
            --border-color: #ecf0f1;
            --shadow-color: rgba(0,0,0,0.05);
            --pending-accent: #f39c12;
            --completed-accent: #27ae60;
            --processing-accent: #2980b9;
            --cancelled-accent: #c0392b;
        }
        body{ margin:0; font-family:'Poppins', Arial, sans-serif; background:var(--main-bg); color:var(--text-dark); font-size:15px; }

        .sidebar{ position:fixed; top:0; left:0; width:240px; height:100%; background:var(--sidebar-bg); color:var(--text-light); display:flex; flex-direction:column; box-shadow:4px 0 20px rgba(0,0,0,0.1); z-index:100; }
        .sidebar-header{ padding:25px 20px; text-align:center; font-size:1.6em; font-weight:600; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar a{ color:var(--text-light); text-decoration:none; padding:16px 25px; display:flex; align-items:center; gap:15px; font-weight:500; border-left:4px solid transparent; transition:all .3s; }
        .sidebar a:hover, .sidebar a.active{ background:rgba(0,0,0,0.2); border-left-color:var(--text-light); }
        .sidebar a.logout{ margin-top:auto; border-top:1px solid rgba(255,255,255,0.1); }

        .main{ margin-left:240px; padding:35px; }
        .main-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:35px; }
        .main-header h1{ font-weight:600; font-size:2em; margin:0; }
        .user-info{ background:var(--card-bg); padding:8px 16px; border-radius:50px; font-size:0.9em; font-weight:500; border:1px solid var(--border-color); }

        .card-container{ display:grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap:30px; }
        .card{ background:var(--card-bg); padding:25px; border-radius:12px; border:1px solid var(--border-color); box-shadow:0 5px 15px var(--shadow-color); display:flex; flex-direction:column; transition: transform .3s, box-shadow .3s; }
        .card:hover{ transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.08); }
        .card h3{ margin:0 0 15px 0; font-size:1.1em; font-weight:600; color:var(--text-dark); display:flex; align-items:center; gap:10px; border-bottom:1px solid var(--border-color); padding-bottom:12px; }
        .card h3 i{ color:var(--primary-accent); }
        .card-count{ font-size:2.5em; font-weight:700; color:var(--primary-accent); margin:10px 0; text-align:center; }
        .card p{ font-size:0.9em; line-height:1.6; color:var(--text-muted); text-align:center; flex-grow:1; }

        .card.card-pending h3 i, .card.card-pending .card-count, .card.card-pending a { color:var(--pending-accent); }

        .card ol{ list-style:none; padding:0; margin:0; flex-grow:1; }
        .card li{ display:flex; justify-content:space-between; align-items:center; padding:10px 5px; font-size:0.95em; border-bottom:1px solid var(--border-color); }
        .card li:last-child{ border-bottom:none; }
        .card li span{ color:var(--text-muted); font-size:0.9em; }

        .card a{ text-decoration:none; color:var(--primary-accent); font-weight:600; display:block; margin-top:12px; text-align:center; font-size:0.95em; }
        .card a:hover{ text-decoration:underline; }

        .full-width-card{ grid-column:1 / -1; }
        .chart-container{ position:relative; height:350px; width:100%; }

        .recent-orders-table{ width:100%; border-collapse:collapse; margin-top:15px; font-size:0.95em; }
        .recent-orders-table th, .recent-orders-table td{ text-align:left; padding:14px 10px; border-bottom:1px solid var(--border-color); }
        .recent-orders-table thead th{ background:#f9fafb; font-weight:600; color:var(--text-muted); text-transform:uppercase; font-size:0.85em; letter-spacing:0.5px; }
        .recent-orders-table tbody tr:hover{ background:#f9f9f9; }
        .recent-orders-table tr:last-child td{ border-bottom:none; }

        .status-badge{ padding:5px 12px; border-radius:50px; font-size:0.8em; color:white; font-weight:600; text-transform:capitalize; display:inline-block; }

        .status-pending { background-color: var(--pending-accent); }
        .status-pending-order { background-color: var(--pending-accent); }
        .status-pending-payment { background-color: var(--pending-accent); }
        .status-completed { background-color: var(--completed-accent); }
        .status-processing { background-color: var(--processing-accent); }
        .status-cancelled { background-color: var(--cancelled-accent); }

        @media (max-width:900px){ .card-container{ gap:18px; } .card{ padding:18px; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="manage_orders.php"><i class="fa-solid fa-box"></i> Manage Orders</a>
            <a href="menu.php"><i class="fa-solid fa-burger"></i> Manage Menu</a>
            <a href="offers.php"><i class="fa-solid fa-tags"></i> Manage Offers</a>
            <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="admin_con.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>Welcome, Admin 👋</h1>
            <div class="user-info">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></div>
        </div>

        <div class="card-container">
            <div class="card">
                <h3><i class="fa-solid fa-sack-dollar"></i> Today's Revenue</h3>
                <div class="card-count">₹<?php echo number_format($total_today_revenue, 2); ?></div>
                <p>Revenue from processing & completed orders today.</p>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-calendar-days"></i> This Month's Revenue</h3>
                <div class="card-count">₹<?php echo number_format($total_month_revenue, 2); ?></div>
                <p>Revenue from processing & completed orders this month.</p>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-fire"></i> Most Popular Items</h3>
                <?php if ($most_sold_items && $most_sold_items->num_rows > 0): ?>
                    <ol>
                        <?php while($item = $most_sold_items->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($item['item_name']); ?> <span>Sold: <?php echo $item['total_sold']; ?></span></li>
                        <?php endwhile; ?>
                    </ol>
                <?php else: ?>
                    <p>No sales data available yet.</p>
                <?php endif; ?>
            </div>

            <div class="card card-pending">
                <h3><i class="fa-solid fa-hourglass-half"></i> Pending Orders</h3>
                <div class="card-count"><?php echo $total_pending_orders; ?></div>
                <p>New orders awaiting your approval.</p>

                <?php if ($pending_orders_result && $pending_orders_result->num_rows > 0): ?>
                    <ol style="margin-top:12px;">
                        <?php while($p = $pending_orders_result->fetch_assoc()): ?>
                            <li style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                                <div>
                                    <strong><a href="manage_orders.php?id=<?php echo urlencode($p['id']); ?>"><?php echo htmlspecialchars($p['id']); ?></a></strong>
                                </div>
                                <div style="color:var(--text-muted); font-size:0.9em;">
                                    <?php echo date('d M, h:i A', strtotime($p['created_at'])); ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ol>
                <?php else: ?>
                    <p style="margin-top:12px; color:var(--text-muted);">No pending orders right now.</p>
                <?php endif; ?>

                <a href="manage_orders.php">Review Orders →</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-box"></i> Total Orders</h3>
                <div class="card-count"><?php echo $total_orders; ?></div>
                <p>View all incoming customer orders.</p>
                <a href="manage_orders.php">Go to Orders →</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-burger"></i> Menu Items</h3>
                <div class="card-count"><?php echo $total_menu_items; ?></div>
                <p>Add, edit, or remove items from your menu.</p>
                <a href="menu.php">Go to Menu →</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-tags"></i> Active Offers</h3>
                <div class="card-count"><?php echo $total_offers; ?></div>
                <p>Create and manage special offers.</p>
                <a href="offers.php">Go to Offers →</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-users"></i> Total Users</h3>
                <div class="card-count"><?php echo $total_users; ?></div>
                <p>View and manage all registered user accounts.</p>
                <a href="manage_users.php">Go to Users →</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-envelope"></i> Contact Messages</h3>
                <div class="card-count"><?php echo $total_messages; ?></div>
                <p>Read and respond to inquiries from customers.</p>
                <a href="admin_con.php">Go to Messages →</a>
            </div>

            <div class="card full-width-card">
                <h3><i class="fa-solid fa-chart-line"></i> Weekly Revenue Trend</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="card full-width-card">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</h3>
                <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                    <table class="recent-orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $recent_orders_result->fetch_assoc()):
                                $status_class = status_to_class($order['order_status']);
                            ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td>₹<?php echo number_format($order['grand_total'], 2); ?></td>
                                <td>
                                    <span class="status-badge <?php echo htmlspecialchars($status_class); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align:center; padding:20px;">No recent orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch('get_chart_data.php')
            .then(response => response.json())
            .then(chartData => {
                if (chartData.error) { console.error('Error from get_chart_data.php:', chartData.error); return; }
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Daily Revenue',
                            data: chartData.data,
                            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-accent') || '#e67e22',
                            backgroundColor: 'rgba(230, 126, 34, 0.1)',
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₹' + value; } } } },
                        plugins: { legend: { display: false } }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    });
    </script>
</body>
</html>
