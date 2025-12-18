<?php
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$user_id = intval($_POST['user_id'] ?? 0);
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || $new_password !== $confirm_password) {
    header("Location: profile.php?status=error");
    exit;
}

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt_verify = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt_verify->bind_param("s", $username);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result()->fetch_assoc();
$stmt_verify->close();

if (!$result_verify || $result_verify['id'] != $user_id) {
    header("Location: profile.php?status=error");
    exit;
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt_update->bind_param("si", $hashed_password, $user_id);

if ($stmt_update->execute()) {

    header("Location: profile.php?status=pwdsuccess");
} else {

    header("Location: profile.php?status=error");
}

$stmt_update->close();
$conn->close();
exit;
?>