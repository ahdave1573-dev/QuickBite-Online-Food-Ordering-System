<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache"); 
header("Expires: 0");

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>