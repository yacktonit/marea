<?php
session_start();
require '../includes/template/header_admin.php';
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}
require '../includes/db.php';

// Funzione per escape output HTML
function e($str) {
    return htmlspecialchars($str);
}

// Variabili per messaggi
$messaggio = '';
$errore = '';

// Gestione aggiunta/modifica ombrellone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salva'])) {
    $id = $_POST['id'] ?? null;
    $numero = trim($_POST['numero'] ?? '');
    $fila = trim($_POST['fila'] ?? '');

    if ($numero === '' || $fila === '') {
        $errore = "Numero e Fila sono obbligatori.";
    } else {
        if ($id) {
            // Modifica
            $stmt = $conn->prepare("UPDATE ombrelloni SET numero = ?, fila = ? WHERE id = ?");
            $stmt->execute([$numero, $fila, $id]);
            $messaggio = "Ombrellone aggiornato.";
        } else {
            // Nuovo
            $stmt = $conn->prepare("INSERT INTO ombrelloni (numero, fila) VALUES (?, ?)");
            $stmt->execute([$numero, $fila]);
            $messaggio = "Nuovo ombrellone aggiunto.";
        }
    }
}

// Gestione eliminazione ombrellone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina'])) {
    $id = $_POST['id'] ?? null;
    if ($id) {
        // Puoi aggiungere controllo se è usato in ordini, ma per ora eliminiamo direttamente
        $stmt = $conn->prepare("DELETE FROM ombrelloni WHERE id = ?");
        $stmt->execute([$id]);
        $messaggio = "Ombrellone eliminato.";
    }
}

// Per modificare, prendi i dati da GET ?edit=id
$edit_ombrellone = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM ombrelloni WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_ombrellone = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Prendi lista ombrelloni
$stmt = $conn->query("SELECT * FROM ombrelloni ORDER BY fila, numero");
$ombrelloni = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Gestisci Ombrelloni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body">
                        <h2 class="card-title text-primary mb-4"><i class="bi bi-umbrella"></i> Gestisci Ombrelloni</h2>
                        <?php if ($messaggio): ?>
                            <div class="alert alert-success text-center" role="alert"><?= e($messaggio) ?></div>
                        <?php endif; ?>
                        <?php if ($errore): ?>
                            <div class="alert alert-danger text-center" role="alert"><?= e($errore) ?></div>
                        <?php endif; ?>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Numero</th>
                                        <th>Fila</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ombrelloni as $o): ?>
                                        <tr>
                                            <td><?= e($o['id']) ?></td>
                                            <td><?= e($o['numero']) ?></td>
                                            <td><?= e($o['fila']) ?></td>
                                            <td class="actions">
                                                <a href="?edit=<?= e($o['id']) ?>" class="btn btn-sm btn-outline-primary mb-1"><i class="bi bi-pencil"></i> Modifica</a>
                                                <form method="post" onsubmit="return confirm('Eliminare ombrellone #<?= e($o['numero']) ?>?');" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= e($o['id']) ?>">
                                                    <button type="submit" name="elimina" class="btn btn-sm btn-outline-danger mb-1"><i class="bi bi-trash"></i> Elimina</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card p-3 mb-3">
                            <h4 class="mb-3 text-secondary">
                                <i class="bi bi-plus-circle"></i> <?= $edit_ombrellone ? "Modifica Ombrellone #".e($edit_ombrellone['id']) : "Aggiungi Nuovo Ombrellone" ?>
                            </h4>
                            <form method="post" class="row g-3 align-items-end">
                                <input type="hidden" name="id" value="<?= e($edit_ombrellone['id'] ?? '') ?>">
                                <div class="col-md-4">
                                    <label class="form-label">Numero</label>
                                    <input type="text" name="numero" value="<?= e($edit_ombrellone['numero'] ?? '') ?>" required class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fila</label>
                                    <input type="text" name="fila" value="<?= e($edit_ombrellone['fila'] ?? '') ?>" required class="form-control">
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" name="salva" class="btn btn-success flex-grow-1">
                                        <i class="bi bi-check-circle"></i> <?= $edit_ombrellone ? "Aggiorna" : "Aggiungi" ?>
                                    </button>
                                    <?php if ($edit_ombrellone): ?>
                                        <a href="gestisci_ombrelloni.php" class="btn btn-secondary flex-grow-1">Annulla</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                        <div class="text-center mt-3">
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
