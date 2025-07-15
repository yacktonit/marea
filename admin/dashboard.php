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
    <style>
        body { font-family: Arial, sans-serif; padding: 2em; }
        h1 { margin-bottom: 1em; }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1.2em;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Dashboard Amministratore</h1>
    <nav>
        <a href="gestisci_ordini.php" class="btn">Gestione Ordini</a>
        <a href="gestisci_prodotti.php" class="btn">Gestisci Prodotti</a>
        <a href="gestisci_ombrelloni.php" class="btn">Gestisci Ombrelloni</a>
    </nav>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
