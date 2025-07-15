<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
</head>
<body>
    <h1>Dashboard Amministratore</h1>
    <nav>
        <a href="gestisci_ordini.php" class="btn">Gestione Ordini</a> |
        <a href="gestisci_prodotti.php" class="btn">Gestisci Prodotti</a> |
        <a href="gestisci_ombrelloni.php" class="btn">Gestisci Ombrelloni</a>
    </nav>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
