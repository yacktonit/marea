<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}
require '../includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID ordine mancante.");
}

// Prendi info ordine e ombrellone
$stmt = $conn->prepare("
    SELECT o.*, om.numero AS numero_ombrellone
    FROM ordini o
    JOIN ombrelloni om ON o.id_ombrellone = om.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$ordine = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ordine) {
    die("Ordine non trovato.");
}

// Prendi dettagli prodotti dell'ordine
$stmtDettagli = $conn->prepare("
    SELECT d.*, p.nome, p.prezzo
    FROM dettagli_ordini d
    JOIN prodotti p ON d.id_prodotto = p.id
    WHERE d.id_ordine = ?
");
$stmtDettagli->execute([$id]);
$dettagli = $stmtDettagli->fetchAll(PDO::FETCH_ASSOC);

// Gestione aggiornamento stato ordine
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuovo_stato = $_POST['stato'] ?? '';
    $stati_validi = ['inviato', 'in preparazione', 'evaso'];
    if (in_array($nuovo_stato, $stati_validi)) {
        $update = $conn->prepare("UPDATE ordini SET stato = ? WHERE id = ?");
        $update->execute([$nuovo_stato, $id]);
        header("Location: dettaglio_ordine.php?id=$id&msg=stato_aggiornato");
        exit();
    } else {
        $errore = "Stato non valido.";
    }
}

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
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Dettaglio Ordine #<?= htmlspecialchars($ordine['id']) ?></h2>
    <p><strong>Ombrellone:</strong> <?= htmlspecialchars($ordine['numero_ombrellone']) ?></p>
    <p><strong>Data/Ora:</strong> <?= htmlspecialchars($ordine['data_ora']) ?></p>
    <p><strong>Totale:</strong> € <?= number_format($ordine['totale'], 2) ?></p>

    <form method="post">
        <label for="stato">Stato ordine:</label>
        <select name="stato" id="stato">
            <?php
            $stati = ['inviato', 'in preparazione', 'evaso'];
            foreach ($stati as $s):
                $selected = ($ordine['stato'] === $s) ? 'selected' : '';
                echo "<option value=\"$s\" $selected>" . ucfirst($s) . "</option>";
            endforeach;
            ?>
        </select>
        <button type="submit">Aggiorna Stato</button>
    </form>
    <?php if (isset($errore)): ?>
        <p style="color:red;"><?= htmlspecialchars($errore) ?></p>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'stato_aggiornato'): ?>
        <p style="color:green;">Stato aggiornato con successo.</p>
    <?php endif; ?>

    <h3>Prodotti ordinati</h3>
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

    <p><a href="dashboard.php">Torna alla Dashboard</a></p>
</body>
</html>
