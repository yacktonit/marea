<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}
require '../includes/db.php';

$stmt = $conn->query("
    SELECT o.id, om.numero, o.data_ora, o.stato, o.totale
    FROM ordini o
    JOIN ombrelloni om ON o.id_ombrellone = om.id
    ORDER BY o.data_ora DESC
");
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
    <title>Dashboard Admin</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: center; }
        .msg-success { color: green; margin-bottom: 1em; }
        button.elimina-btn {
            color: white;
            background-color: red;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
        button.elimina-btn:hover {
            background-color: darkred;
        }
        form { display: inline; }
    </style>
</head>
<body>
    <h2>Gestione Ordini</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ordine_eliminato'): ?>
        <p class="msg-success">Ordine eliminato con successo.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Ordine</th>
                <th>Ombrellone</th>
                <th>Data/Ora</th>
                <th>Stato</th>
                <th>Totale</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ordini as $ordine): ?>
            <tr>
                <td>
                    <a href="dettaglio_ordine.php?id=<?= $ordine['id'] ?>">
                        #<?= htmlspecialchars($ordine['id']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($ordine['numero']) ?></td>
                <td><?= htmlspecialchars($ordine['data_ora']) ?></td>
                <td style="color: <?= coloreStato($ordine['stato']) ?>;">
                    <?= htmlspecialchars($ordine['stato']) ?>
                </td>
                <td>€ <?= number_format($ordine['totale'], 2) ?></td>
                <td>
                    <form method="post" action="elimina_ordine.php" onsubmit="return confirm('Sei sicuro di voler eliminare l\'ordine #<?= $ordine['id'] ?>?');">
                        <input type="hidden" name="id" value="<?= $ordine['id'] ?>">
                        <button type="submit" class="elimina-btn">Elimina</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="gestisci_prodotti.php">Gestisci Prodotti</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
