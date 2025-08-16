<?php
session_start();
require '../includes/db.php';
require '../includes/template/header_cliente.php';

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
                        <h2 class="card-title text-primary text-center mb-4"><i class="bi bi-receipt"></i> I tuoi ordini</h2>
                        <?php if (empty($ordini)): ?>
                            <div class="alert alert-info text-center">Nessun ordine effettuato.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-primary">
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
                                            <td>
                                                <?php
                                                $stato = strtolower($ordine['stato']);
                                                $badgeClass = 'badge-stato-altro';
                                                if ($stato === 'inviato') $badgeClass = 'badge-stato-inviato';
                                                elseif ($stato === 'in preparazione') $badgeClass = 'badge-stato-in-preparazione';
                                                elseif ($stato === 'evaso') $badgeClass = 'badge-stato-evaso';
                                                ?>
                                                <span class="badge <?= $badgeClass ?> px-3 py-2">
                                                    <?= htmlspecialchars(ucfirst($ordine['stato'])) ?>
                                                </span>
                                            </td>
                                            <td><strong>€ <?= number_format($ordine['totale'], 2) ?></strong></td>
                                            <td>
                                                <a href="dettaglio_ordine_cliente.php?id=<?= $ordine['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Vedi
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
