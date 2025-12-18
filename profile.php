<?php

require_once 'auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'] ?? '';
$user_details = [];
$orders = [];

$pending_variants = ['Pending', 'Pending Order', 'Pending Payment'];

function normalize_status($status, $pending_variants) {
    $status = (string)$status;

    $cmp = trim($status);
    foreach ($pending_variants as $pv) {
        if (strcasecmp($cmp, $pv) === 0) {
            return ['display' => 'Pending', 'class' => 'status-pending'];
        }
    }

    if (stripos($cmp, 'complete') !== false) return ['display' => 'Completed', 'class' => 'status-completed'];
    if (stripos($cmp, 'process') !== false) return ['display' => 'Processing', 'class' => 'status-processing'];
    if (stripos($cmp, 'cancel') !== false) return ['display' => 'Cancelled', 'class' => 'status-cancelled'];

    $s = strtolower($cmp);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    $class = $s ? 'status-' . $s : 'status-unknown';
    return ['display' => $status, 'class' => $class];
}

if ($username) {
    $stmt_user = $conn->prepare("SELECT id, username, email, phone FROM users WHERE username = ?");
    if ($stmt_user) {
        $stmt_user->bind_param("s", $username);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user) {
            $user_details = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    }
}

$stmt_orders = $conn->prepare("SELECT id, grand_total, order_status, created_at FROM orders WHERE username = ? ORDER BY created_at DESC");
if ($stmt_orders) {
    $stmt_orders->bind_param("s", $username);
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();
    if ($result_orders) {
        while ($order_row = $result_orders->fetch_assoc()) {
            $items = [];
            $items_stmt = $conn->prepare("SELECT item_name, quantity FROM order_items WHERE order_id = ?");
            if ($items_stmt) {
                $items_stmt->bind_param("i", $order_row['id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                if ($items_result) {
                    while ($item_row = $items_result->fetch_assoc()) {
                        $items[] = $item_row;
                    }
                }
                $items_stmt->close();
            }

            $norm = normalize_status($order_row['order_status'], $pending_variants);
            $order_row['display_status'] = $norm['display'];
            $order_row['status_class'] = $norm['class'];

            $order_row['items'] = $items;
            $orders[] = $order_row;
        }
    }
    $stmt_orders->close();
}
$conn->close();

$page_title = "My Profile - QuickBite";
require_once 'header.php';
?>

<style>
    .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 500; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
    .profile-card, .form-card {
        background-color: #fff; padding: 25px 30px; border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 40px;
    }
    .profile-card h2, .form-card h2 {
        margin-top: 0; margin-bottom: 20px; font-size: 1.8em;
        color: var(--primary-dark); border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;
    }
    .profile-card p { font-size: 1.1em; line-height: 1.8; color: #555; margin: 10px 0; }
    .profile-card strong { color: var(--text-dark); min-width: 120px; display: inline-block; }
    .action-buttons { margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap; }
    .btn {
        padding: 10px 20px; border: none; border-radius: 8px;
        font-size: 1em; font-weight: 500; cursor: pointer;
        transition: background-color 0.3s; color: white; text-decoration: none;
    }
    .btn-edit { background-color: #3498db; }
    .btn-password { background-color: #9b59b6; }
    .btn-submit { background-color: var(--primary-color); }
    .btn-cancel { background-color: #7f8c8d; }
    .form-card { display: none; }
    .form-group { margin-bottom: 15px; text-align: left; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
    .form-group input {
        width: 100%; padding: 10px; border-radius: 6px;
        border: 1px solid #ccc; box-sizing: border-box;
    }
    .section-title {
        text-align: center; font-size: 2.2em;
        margin-bottom: 30px; color: var(--primary-dark);
    }
    .order-history-table {
        width: 100%; border-collapse: collapse;
        background-color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 12px; overflow: hidden;
    }
    .order-history-table th, .order-history-table td {
        padding: 15px; text-align: left; border-bottom: 1px solid #eee;
    }
    .order-history-table thead {
        background-color: var(--primary-color); color: white; font-size: 1.1em;
    }
    .status {
        padding: 5px 10px; border-radius: 15px; color: white;
        font-weight: 500; font-size: 0.9em; text-align: center; display: inline-block;
    }
    .status-completed { background-color: #27ae60; }
    .status-pending, .status-pending-payment, .status-pending-order { background-color: #f39c12; }
    .status-processing { background-color: #3498db; }
    .status-cancelled { background-color: #c0392b; }
    .order-items ul { list-style: none; padding: 0; margin: 0; }
    .order-items li { font-size: 0.95em; color: #333; }
    .order-items li .item-qty { font-size: 0.9em; color: #777; }
</style>

<div class="container">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <p class="message success">✅ Profile updated successfully!</p>
        <?php elseif ($_GET['status'] == 'pwdsuccess'): ?>
            <p class="message success">🔒 Password changed successfully!</p>
        <?php elseif ($_GET['status'] == 'error'): ?>
            <p class="message error">⚠️ An error occurred. Please try again.</p>
        <?php elseif ($_GET['status'] == 'exists'): ?>
            <p class="message error">⚠️ Email or phone number is already in use by another account.</p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="profile-card" id="profile-display">
        <h2>👤 My Profile Details</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($user_details['username'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user_details['email'] ?? 'Not Set') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user_details['phone'] ?? 'Not Set') ?></p>
        <div class="action-buttons">
            <button class="btn btn-edit" onclick="toggleEditForm()">Edit Profile</button>
            <button class="btn btn-password" onclick="togglePasswordForm()">Change Password</button>
        </div>
    </div>

    <div class="form-card" id="profile-edit-form">
        <h2>✏️ Edit Profile</h2>
        <form action="update_profile.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_details['id'] ?? '' ?>">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user_details['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user_details['phone'] ?? '') ?>" required pattern="[0-9]{10}" title="Please enter a 10-digit phone number">
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-submit">💾 Save Changes</button>
                <button type="button" class="btn btn-cancel" onclick="toggleEditForm()">Cancel</button>
            </div>
        </form>
    </div>

    <div class="form-card" id="password-change-form">
        <h2>🔒 Change Password</h2>
        <form action="update_password.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_details['id'] ?? '' ?>">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-submit">🔁 Update Password</button>
                <button type="button" class="btn btn-cancel" onclick="togglePasswordForm()">Cancel</button>
            </div>
        </form>
    </div>

    <h2 class="section-title">📜 My Order History</h2>
    <table class="order-history-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Items</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th style="text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="5" style="text-align:center; padding:40px; color:#777;">You have no order history yet.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td class="order-items">
                            <ul>
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?= htmlspecialchars($item['item_name']) ?> <span class="item-qty">(x<?= (int)$item['quantity'] ?>)</span></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li style="color:#777;">(No items recorded)</li>
                                <?php endif; ?>
                            </ul>
                        </td>
                        <td>₹<?= number_format((float)$order['grand_total'], 2) ?></td>
                        <td><?= date("d M, Y", strtotime($order['created_at'])) ?></td>
                        <td style="text-align:center;">
                            <span class="status <?= htmlspecialchars($order['status_class']) ?>"><?= htmlspecialchars($order['display_status']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleEditForm() {
    document.getElementById('profile-display').style.display = 'none';
    document.getElementById('password-change-form').style.display = 'none';
    const form = document.getElementById('profile-edit-form');
    form.style.display = (form.style.display === 'block') ? 'none' : 'block';
    if (form.style.display === 'none') document.getElementById('profile-display').style.display = 'block';
}

function togglePasswordForm() {
    document.getElementById('profile-display').style.display = 'none';
    document.getElementById('profile-edit-form').style.display = 'none';
    const form = document.getElementById('password-change-form');
    form.style.display = (form.style.display === 'block') ? 'none' : 'block';
    if (form.style.display === 'none') document.getElementById('profile-display').style.display = 'block';
}
</script>

<?php require_once 'footer.php'; ?>
