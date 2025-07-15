<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        die("ID ordine mancante.");
    }

    try {
        // Elimina prima i dettagli dell’ordine
        $stmtDettagli = $conn->prepare("DELETE FROM dettagli_ordini WHERE id_ordine = ?");
        $stmtDettagli->execute([$id]);

        // Elimina l’ordine
        $stmtOrdine = $conn->prepare("DELETE FROM ordini WHERE id = ?");
        $stmtOrdine->execute([$id]);

        header("Location: dashboard.php?msg=ordine_eliminato");
        exit();
    } catch (Exception $e) {
        die("Errore durante eliminazione ordine: " . $e->getMessage());
    }
} else {
    die("Metodo non consentito.");
}
