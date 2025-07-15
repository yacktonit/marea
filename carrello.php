<?php
session_start();
require 'includes/db.php';

// Verifica accesso cliente
if (!isset($_SESSION['ombrellone_id'])) {
    header('Location: index.php');
    exit();
}

// Caricamento prodotti solo per validazione (non li mostriamo tutti qui)
$stmt = $conn->query("SELECT * FROM prodotti");
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
$prodotti_map = [];
foreach ($prodotti as $p) {
    $prodotti_map[$p['id']] = $p;
}

// Gestione carrello in sessione
if (!isset($_SESSION['carrello'])) {
    $_SESSION['carrello'] = [];
}

// Aggiunta al carrello da prodotti.php (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi'])) {
    $id = $_POST['id_prodotto'];
    $quantita = max(1, intval($_POST['quantita']));
    $_SESSION['carrello'][$id] = ($_SESSION['carrello'][$id] ?? 0) + $quantita;
    header('Location: carrello.php');
    exit();
}

// Rimozione dal carrello
if (isset($_GET['rimuovi'])) {
    unset($_SESSION['carrello'][$_GET['rimuovi']]);
    header('Location: carrello.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Carrello</title></head>
<body>
    <h2>Carrello</h2>

    <?php if (empty($_SESSION['carrello'])): ?>
        <p>Il carrello è vuoto.</p>
        <p><a href="prodotti.php">Vai a Ordina Ora</a></p>
    <?php else: ?>
        <form method="post" action="invia_ordine.php">
            <table border="1" cellpadding="5" cellspacing="0">
                <tr><th>Prodotto</th><th>Quantità</th><th>Prezzo Unitario</th><th>Subtotale</th><th></th></tr>
                <?php
                $totale = 0;
                foreach ($_SESSION['carrello'] as $id => $q):
                    if (!isset($prodotti_map[$id])) continue; // sicurezza
                    $p = $prodotti_map[$id];
                    $sub = $p['prezzo'] * $q;
                    $totale += $sub;
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= $q ?></td>
                    <td>€ <?= number_format($p['prezzo'], 2) ?></td>
                    <td>€ <?= number_format($sub, 2) ?></td>
                    <td><a href="?rimuovi=<?= $id ?>" onclick="return confirm('Rimuovere prodotto dal carrello?')">Rimuovi</a></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align:right;"><strong>Totale</strong></td>
                    <td colspan="2">€ <?= number_format($totale, 2) ?></td>
                </tr>
            </table>
            <br>
            <button type="submit">Invia Ordine</button>
        </form>
        <p><a href="prodotti.php">Aggiungi altri prodotti</a></p>
    <?php endif; ?>

    <p><a href="home.php">Torna alla Home</a></p>
</body>
</html>
