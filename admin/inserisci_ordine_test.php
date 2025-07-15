<?php
require '../includes/db.php';

// Dati esempio
$id_ombrellone = 1;   // Assicurati che esista
$data_ora = date('Y-m-d H:i:s');
$stato = 'inviato';
$totale = 15.50;      // Esempio

try {
    // Inserisci ordine
    $stmt = $conn->prepare("INSERT INTO ordini (id_ombrellone, data_ora, stato, totale) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_ombrellone, $data_ora, $stato, $totale]);
    $id_ordine = $conn->lastInsertId();

    // Prendi prezzo prodotti per inserirlo come prezzo_unitario
    $stmtProd1 = $conn->prepare("SELECT prezzo FROM prodotti WHERE id = ?");
    $stmtProd1->execute([1]);
    $prezzo1 = floatval($stmtProd1->fetchColumn());

    $stmtProd2 = $conn->prepare("SELECT prezzo FROM prodotti WHERE id = ?");
    $stmtProd2->execute([2]);
    $prezzo2 = floatval($stmtProd2->fetchColumn());

    // Inserisci dettagli ordini (esempio 2 prodotti)
    $stmtDettagli = $conn->prepare("INSERT INTO dettagli_ordini (id_ordine, id_prodotto, quantita, prezzo_unitario) VALUES (?, ?, ?, ?)");

    $stmtDettagli->execute([$id_ordine, 1, 2, $prezzo1]);  // 2 pezzi prodotto 1
    $stmtDettagli->execute([$id_ordine, 2, 1, $prezzo2]);  // 1 pezzo prodotto 2

    echo "Ordine di prova inserito con ID: $id_ordine";
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
}
