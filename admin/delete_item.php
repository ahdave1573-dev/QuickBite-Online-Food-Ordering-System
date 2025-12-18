<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit; }

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$id = $_GET['id'];
$conn->query("DELETE FROM menu_items WHERE id = $id");

header("Location: admin_dashboard.php");
exit;
?>
