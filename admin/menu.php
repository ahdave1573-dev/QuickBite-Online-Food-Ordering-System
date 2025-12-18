<?php
require_once 'admin_auth_check.php';
$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$sql = "SELECT id, name, price, category, image, is_available FROM menu_items ORDER BY category, name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
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
            --available-color: #10b981;
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
        
        .item-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .item-details img { 
            width: 60px; height: 60px; 
            object-fit: cover; 
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        .item-info .item-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.05em;
        }
        .item-info .item-category {
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
        .edit-btn { background: var(--edit-color); }
        .delete-btn { background: var(--delete-color); }

        .availability-switch { position: relative; display: inline-block; width: 50px; height: 28px; }
        .availability-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 28px; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--available-color); }
        input:checked + .slider:before { transform: translateX(22px); }
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
        <div class="main-header">
            <h1>Manage Menu Items</h1>
        </div>
        <div class="content-card">
            <div class="content-header">
                <p>Add, edit, or remove items from your restaurant's menu.</p>
                <a href="add_item.php" class="add-btn"><i class="fa-solid fa-plus"></i> Add New Item</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price (₹)</th>
                        <th>Availability</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="item-details">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars(basename($row['image'])) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                    <?php else: ?>
                                        <img src="placeholder.jpg" alt="No image">
                                    <?php endif; ?>
                                    <div class="item-info">
                                        <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                                        <div class="item-category"><?= htmlspecialchars($row['category']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><strong><?= number_format($row['price'], 2) ?></strong></td>
                            <td>
                                <label class="availability-switch">
                                    <input type="checkbox" class="status-toggle" data-id="<?php echo $row['id']; ?>" <?php echo ($row['is_available'] == 1) ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td style="text-align: right;">
                                <a href="edit_item.php?id=<?= $row['id'] ?>" class="action-btn edit-btn"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="delete_item.php?id=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this item?')"><i class="fa-solid fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 25px;">No menu items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const itemId = this.dataset.id;
                const status = this.checked ? 1 : 0;
                const formData = new FormData();
                formData.append('id', itemId);
                formData.append('status', status);
                
                fetch('update_availability.php', { 
                    method: 'POST', 
                    body: formData 
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        alert('Error updating status: ' + data.message);
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    alert('An unexpected error occurred. Please check the console for details.');
                    this.checked = !this.checked;
                });
            });
        });
    });
    </script>
</body>
</html>