<?php
require_once 'admin_auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php?deleted=true");
    exit;
}

$sql = "SELECT id, username, email, phone FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
            --delete-color: #ef4444;
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
        
        .user-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--border-color);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
        }
        .user-info .user-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        .user-info .user-email {
            font-size: 0.9em;
            color: var(--text-muted);
        }

        .action-btn { 
            text-decoration: none; padding: 7px 12px; border-radius: 6px; 
            color: white; margin: 2px; font-size: 0.85em; font-weight: 500; 
            transition: all 0.2s ease; display: inline-flex; align-items: center; 
            gap: 5px; border: none; cursor: pointer;
        }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-delete { background-color: var(--delete-color); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">Admin Panel</div>
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="manage_orders.php"><i class="fa-solid fa-box"></i> Manage Orders</a>
            <a href="menu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a>
            <a href="offers.php"><i class="fa-solid fa-tags"></i> Manage Offers</a>
            <a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="admin_con.php"><i class="fa-solid fa-envelope"></i> Messages</a>
            <a href="admin_logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="main-header">
            <h1>Manage Users</h1>
        </div>
        
        <div class="content-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Phone</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= $row['id']; ?></strong></td>
                            <td>
                                <div class="user-details">
                                    <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
                                    <div class="user-info">
                                        <div class="user-name"><?= htmlspecialchars($row['username']); ?></div>
                                        <div class="user-email"><?= htmlspecialchars($row['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['phone']); ?></td>
                            <td style="text-align:center;">
                                <a class="action-btn btn-delete" title="Delete User" href="manage_users.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 25px;">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>