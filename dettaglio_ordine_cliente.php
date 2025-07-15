<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['ombrellone_id'])) {
    header('Location: index.php');
    exit();
}

$id_ordine = $_GET['id'] ?? null;
if (!$id_ordine) {
    die("ID ordine mancante.");
}

// Controlla che l'ordine appartenga all'ombrellone della sessione
$stmt = $conn->prepare("
    SELECT o.*, om.numero AS numero_ombrellone
    FROM ordini o
    JOIN ombrelloni om ON o.id_ombrellone = om.id
    WHERE o.id = ? AND o.id_ombrellone = ?
");
$stmt->execute([$id_ordine, $_SESSION['ombrellone_id']]);
$ordine = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ordine) {
    die("Ordine non trovato o accesso negato.");
}

// Prendi dettagli prodotti dell'ordine
$stmtDettagli = $conn->prepare("
    SELECT d.*, p.nome, p.prezzo
    FROM dettagli_ordini d
    JOIN prodotti p ON d.id_prodotto = p.id
    WHERE d.id_ordine = ?
");
$stmtDettagli->execute([$id_ordine]);
$dettagli = $stmtDettagli->fetchAll(PDO::FETCH_ASSOC);

function coloreStato($stato) {
    switch ($stato) {
        case 'inviato': return 'orange';
        case 'in preparazione': return 'blue';
        case 'evaso': return 'green';
        default: return 'black';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Dettaglio Ordine #<?= htmlspecialchars($ordine['id']) ?></title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h2>Dettaglio Ordine #<?= htmlspecialchars($ordine['id']) ?></h2>
    <p><strong>Ombrellone:</strong> <?= htmlspecialchars($ordine['numero_ombrellone']) ?></p>
    <p><strong>Data/Ora:</strong> <?= htmlspecialchars($ordine['data_ora']) ?></p>
    <p><strong>Stato Ordine:</strong> <span style="color: <?= coloreStato($ordine['stato']) ?>; font-weight:bold;"><?= htmlspecialchars(ucfirst($ordine['stato'])) ?></span></p>
    <p><strong>Totale:</strong> € <?= number_format($ordine['totale'], 2) ?></p>

    <h3>Prodotti Ordinati</h3>
    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th>Prezzo Unitario</th>
                <th>Quantità</th>
                <th>Subtotale</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dettagli as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nome']) ?></td>
                <td>€ <?= number_format($d['prezzo'], 2) ?></td>
                <td><?= htmlspecialchars($d['quantita']) ?></td>
                <td>€ <?= number_format($d['prezzo'] * $d['quantita'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="ordini_cliente.php">Torna agli ordini</a></p>
    <p><a href="home.php">Torna alla Home</a></p>
</body>
</html>
