<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "food";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM contact_messages WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: admin_contact.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting message: " . $conn->error;
    }
}

$conn->close();
?>
