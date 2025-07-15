<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: /admin/login.php");
    exit();
}
?>
