<?php
session_start();
require '../includes/template/header_admin.php';
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
                                <p class="mb-1"><strong>Totale:</strong> <span class="fw-bold text-success">€ <?= number_format($ordine['totale'], 2) ?></span></p>
                                <?php
                                $stato = strtolower($ordine['stato']);
                                $badgeClass = 'badge-stato-altro';
                                if ($stato === 'inviato') $badgeClass = 'badge-stato-inviato';
                                elseif ($stato === 'in preparazione') $badgeClass = 'badge-stato-in-preparazione';
                                elseif ($stato === 'evaso') $badgeClass = 'badge-stato-evaso';
                                ?>
                                <p class="mb-1"><strong>Stato ordine:</strong> <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6"><?= htmlspecialchars(ucfirst($ordine['stato'])) ?></span></p>
                            </div>
                        </div>
                        <form method="post" class="row g-2 align-items-end mb-4">
                            <div class="col-auto">
                                <label for="stato" class="form-label mb-0">Cambia stato:</label>
                            </div>
                            <div class="col-auto">
                                <select name="stato" id="stato" class="form-select">
                                    <?php
                                    $stati = ['inviato', 'in preparazione', 'evaso'];
                                    foreach ($stati as $s):
                                        $selected = ($ordine['stato'] === $s) ? 'selected' : '';
                                        echo "<option value=\"$s\" $selected>" . ucfirst($s) . "</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Aggiorna Stato</button>
                            </div>
                        </form>
                        <?php if (isset($errore)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?= htmlspecialchars($errore) ?>
                            </div>
                        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'stato_aggiornato'): ?>
                            <div class="alert alert-success text-center" role="alert">
                                Stato aggiornato con successo.
                            </div>
                        <?php endif; ?>
                        <h4 class="mt-4 mb-3 text-secondary"><i class="bi bi-basket"></i> Prodotti ordinati</h4>
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
