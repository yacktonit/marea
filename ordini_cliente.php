<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['ombrellone_id'])) {
    header('Location: index.php');
    exit();
}

$id_ombrellone = $_SESSION['ombrellone_id'];

$stmt = $conn->prepare("
    SELECT id, data_ora, stato, totale
    FROM ordini
    WHERE id_ombrellone = ?
    ORDER BY data_ora DESC
");
$stmt->execute([$id_ombrellone]);
$ordini = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Ordini Passati</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: center; }
    </style>
</head>
<body>
    <h2>I tuoi ordini</h2>
    <?php if (empty($ordini)): ?>
        <p>Nessun ordine effettuato.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Ordine</th>
                    <th>Data/Ora</th>
                    <th>Stato</th>
                    <th>Totale</th>
                    <th>Dettaglio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordini as $ordine): ?>
                <tr>
                    <td>#<?= htmlspecialchars($ordine['id']) ?></td>
                    <td><?= htmlspecialchars($ordine['data_ora']) ?></td>
                    <td style="color: <?= coloreStato($ordine['stato']) ?>;">
                        <?= htmlspecialchars(ucfirst($ordine['stato'])) ?>
                    </td>
                    <td>€ <?= number_format($ordine['totale'], 2) ?></td>
                    <td>
                        <a href="dettaglio_ordine_cliente.php?id=<?= $ordine['id'] ?>">Vedi</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="home.php">Torna alla Home</a></p>
</body>
</html>
