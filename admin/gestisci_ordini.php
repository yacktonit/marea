<?php
session_start();
require '../includes/template/header_admin.php';
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
    <title>Gestione Ordini</title>
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
            <div class="col-12 col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h2 class="card-title text-primary text-center mb-4"><i class="bi bi-receipt"></i> Gestione Ordini</h2>
                        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ordine_eliminato'): ?>
                            <div class="alert alert-success text-center">Ordine eliminato con successo.</div>
                        <?php endif; ?>
                        <!-- Responsive: tabella su desktop, card su mobile -->
                        <div class="d-none d-md-block table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Ordine</th>
                                        <th>Ombrellone</th>
                                        <th>Data/Ora</th>
                                        <th>Stato</th>
                                        <th>Totale</th>
                                        <th>Dettaglio</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($ordini as $ordine): ?>
                                    <tr>
                                        <td class="fw-semibold">#<?= htmlspecialchars($ordine['id']) ?></td>
                                        <td><?= htmlspecialchars($ordine['numero']) ?></td>
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
                                            <a href="dettaglio_ordine.php?id=<?= $ordine['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Dettaglio
                                            </a>
                                        </td>
                                        <td>
                                            <form method="post" action="elimina_ordine.php" onsubmit="return confirm('Sei sicuro di voler eliminare l\'ordine #<?= $ordine['id'] ?>?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= $ordine['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Elimina</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-block d-md-none">
                            <div class="row g-3">
                                <?php foreach ($ordini as $ordine): ?>
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold text-primary">#<?= htmlspecialchars($ordine['id']) ?></span>
                                                    <span class="badge bg-light border text-dark">Ombrellone <?= htmlspecialchars($ordine['numero']) ?></span>
                                                </div>
                                                <div class="mb-1 small text-muted"><i class="bi bi-clock"></i> <?= htmlspecialchars($ordine['data_ora']) ?></div>
                                                <div class="mb-2">
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
                                                </div>
                                                <div class="mb-2 fw-bold text-success">€ <?= number_format($ordine['totale'], 2) ?></div>
                                                <div class="d-flex gap-2">
                                                    <a href="dettaglio_ordine.php?id=<?= $ordine['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                                        <i class="bi bi-eye"></i> Dettaglio
                                                    </a>
                                                    <form method="post" action="elimina_ordine.php" onsubmit="return confirm('Sei sicuro di voler eliminare l\'ordine #<?= $ordine['id'] ?>?');" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?= $ordine['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger flex-fill"><i class="bi bi-trash"></i> Elimina</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-link text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Torna alla Dashboard
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
