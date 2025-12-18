<?php
require_once 'admin_auth_check.php';

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    exit;
}

header('Content-Type: application/json');

$itemId = (int)$_POST['id'];
$status = (int)$_POST['status'];

if ($status !== 0 && $status !== 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$stmt = $conn->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("ii", $status, $itemId);
    if ($stmt->execute()) {

        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database statement error.']);
}

$conn->close();
?>