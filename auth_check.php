<?php

session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache"); 
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>