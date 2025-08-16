<?php
session_start();
require '../includes/db.php';
require '../includes/template/header_cliente.php';

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
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrello</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4 text-primary">Carrello</h2>
                        <?php if (empty($_SESSION['carrello'])): ?>
                            <div class="alert alert-info text-center">Il carrello è vuoto.</div>
                            <div class="text-center mb-3">
                                <a href="prodotti.php" class="btn btn-success">
                                    <i class="bi bi-bag-plus me-1"></i>Vai a Ordina Ora
                                </a>
                            </div>
                        <?php else: ?>
                            <form method="post" action="invia_ordine.php">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Prodotto</th>
                                                <th>Quantità</th>
                                                <th>Prezzo Unitario</th>
                                                <th>Subtotale</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                                <td>
                                                    <a href="?rimuovi=<?= $id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Rimuovere prodotto dal carrello?')">
                                                        <i class="bi bi-trash"></i> Rimuovi
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                            <tr class="table-secondary">
                                                <td colspan="3" class="text-end"><strong>Totale</strong></td>
                                                <td colspan="2"><strong>€ <?= number_format($totale, 2) ?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-2">
                                    <a href="prodotti.php" class="btn btn-outline-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Aggiungi altri prodotti
                                    </a>
                                    <button type="submit" class="btn btn-success px-4 fw-bold">
                                        <i class="bi bi-send me-1"></i>Invia Ordine
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        <div class="text-center mt-4">
                            <a href="home.php" class="btn btn-link text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Torna alla Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
