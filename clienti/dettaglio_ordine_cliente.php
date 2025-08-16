<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require '../includes/template/header_cliente.php';

if (!isset($_SESSION['ombrellone_id'])) {
    header('Location: index.php');
    exit();
}

$id_ordine = $_GET['id'] ?? null;
if (!$id_ordine) {
    die("ID ordine mancante.");
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .badge-stato-inviato { background: linear-gradient(135deg, #ffb347, #ffcc80); color: #6d4c00; }
        .badge-stato-in-preparazione { background: linear-gradient(135deg, #90caf9, #1976d2); color: #fff; }
        .badge-stato-evaso { background: linear-gradient(135deg, #43e97b, #38f9d7); color: #155724; }
        .badge-stato-altro { background: #bdbdbd; color: #333; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h2 class="card-title text-primary text-center mb-4"><i class="bi bi-receipt"></i> Dettaglio Ordine #<?= htmlspecialchars($ordine['id']) ?></h2>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <p class="mb-1"><strong>Ombrellone:</strong> <?= htmlspecialchars($ordine['numero_ombrellone']) ?></p>
                                <p class="mb-1"><strong>Data/Ora:</strong> <?= htmlspecialchars($ordine['data_ora']) ?></p>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <?php
                                $stato = strtolower($ordine['stato']);
                                $badgeClass = 'badge-stato-altro';
                                if ($stato === 'inviato') $badgeClass = 'badge-stato-inviato';
                                elseif ($stato === 'in preparazione') $badgeClass = 'badge-stato-in-preparazione';
                                elseif ($stato === 'evaso') $badgeClass = 'badge-stato-evaso';
                                ?>
                                <p class="mb-1"><strong>Stato Ordine:</strong> <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6"><?= htmlspecialchars(ucfirst($ordine['stato'])) ?></span></p>
                                <p class="mb-1"><strong>Totale:</strong> <span class="fw-bold text-success">€ <?= number_format($ordine['totale'], 2) ?></span></p>
                            </div>
                        </div>
                        <h4 class="mt-4 mb-3 text-secondary"><i class="bi bi-basket"></i> Prodotti Ordinati</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-primary">
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
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2 mt-4">
                            <a href="ordini_cliente.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Torna agli ordini
                            </a>
                            <a href="home.php" class="btn btn-link text-decoration-none">
                                <i class="bi bi-house"></i> Torna alla Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
