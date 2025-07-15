<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['ombrellone_id']) || empty($_SESSION['carrello'])) {
    header('Location: carrello.php');
    exit();
}

$id_ombrellone = $_SESSION['ombrellone_id'];
$carrello = $_SESSION['carrello'];

// Calcola totale
$totale = 0;
$ids = implode(',', array_keys($carrello));
$stmt = $conn->query("SELECT id, prezzo FROM prodotti WHERE id IN ($ids)");
$prezzi = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

foreach ($carrello as $id => $q) {
    $totale += $prezzi[$id] * $q;
}

// Inserisci ordine
$stmt = $conn->prepare("INSERT INTO ordini (id_ombrellone, data_ora, stato, totale) VALUES (?, NOW(), 'inviato', ?)");
$stmt->execute([$id_ombrellone, $totale]);
$id_ordine = $conn->lastInsertId();

// Inserisci dettagli
$stmtDettagli = $conn->prepare("INSERT INTO dettagli_ordini (id_ordine, id_prodotto, quantita, prezzo_unitario) VALUES (?, ?, ?, ?)");
foreach ($carrello as $id => $q) {
    $stmtDettagli->execute([$id_ordine, $id, $q, $prezzi[$id]]);
}

// Svuota carrello
unset($_SESSION['carrello']);

header("Location: home.php");
exit();
