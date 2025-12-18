<?php
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = intval($_POST['user_id'] ?? 0);
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$current_username = $_SESSION['username'];

if (empty($user_id) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', $phone)) {
    header('Location: profile.php?status=error');
    exit;
}

$stmt_verify = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt_verify->bind_param("s", $current_username);
$stmt_verify->execute();
$result_verify = $stmt_verify->get_result()->fetch_assoc();
$stmt_verify->close();

if (!$result_verify || $result_verify['id'] != $user_id) {
    header("Location: profile.php?status=error");
    exit;
}

$stmt_check = $conn->prepare("SELECT id FROM users WHERE (email = ? OR phone = ?) AND id != ?");
$stmt_check->bind_param("ssi", $email, $phone, $user_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $stmt_check->close();
    $conn->close();

    header("Location: profile.php?status=exists");
    exit;
}
$stmt_check->close();

$stmt_update = $conn->prepare("UPDATE users SET email = ?, phone = ? WHERE id = ?");
$stmt_update->bind_param("ssi", $email, $phone, $user_id);

if ($stmt_update->execute()) {
    header("Location: profile.php?status=success");
} else {

    header("Location: profile.php?status=error");
}

$stmt_update->close();
$conn->close();
exit;
?>