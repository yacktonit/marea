<?php
session_start();
if (!isset($_SESSION['ombrellone_id'])) {
    header("Location: /index.php");
    exit();
}
?>
