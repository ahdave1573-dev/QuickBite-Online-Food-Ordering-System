<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit; }

include "db_connect.php";

$id = $_GET['id'];
$conn->query("DELETE FROM offers WHERE id = $id");

header("Location: admin_offers.php");
exit;
?>
