<?php
session_start();
require '../includes/template/header_admin.php';
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}

require '../includes/db.php';

$action = $_GET['action'] ?? '';

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $prezzo = floatval($_POST['prezzo'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    $eliminaImmagine = isset($_POST['elimina_immagine']);

    $immagine = '';
    $uploadDir = '../uploads/';
    $immagineAggiornata = false;

    if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['immagine']['tmp_name'];
        $fileName = basename($_FILES['immagine']['name']);
        $targetPath = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $fileName);
        if (move_uploaded_file($tmpName, $targetPath)) {
            $immagine = basename($targetPath);
            $immagineAggiornata = true;
        }
    }

    if ($id) {
        $stmt = $conn->prepare("SELECT immagine FROM prodotti WHERE id=?");
        $stmt->execute([$id]);
        $imgPrecedente = $stmt->fetchColumn();

        if ($eliminaImmagine && $imgPrecedente && file_exists($uploadDir . $imgPrecedente)) {
            unlink($uploadDir . $imgPrecedente);
            $immagine = ''; // Nessuna immagine
            $immagineAggiornata = true;
        }

        if ($immagineAggiornata) {
            $stmt = $conn->prepare("UPDATE prodotti SET nome=?, descrizione=?, prezzo=?, categoria=?, immagine=? WHERE id=?");
            $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $immagine, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE prodotti SET nome=?, descrizione=?, prezzo=?, categoria=? WHERE id=?");
            $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $id]);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO prodotti (nome, descrizione, prezzo, categoria, immagine) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $immagine]);
    }

    header('Location: gestisci_prodotti.php');
    exit();
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $stmtImg = $conn->prepare("SELECT immagine FROM prodotti WHERE id=?");
        $stmtImg->execute([$id]);
        $img = $stmtImg->fetchColumn();
        if ($img && file_exists('../uploads/' . $img)) {
            unlink('../uploads/' . $img);
        }
        $stmt = $conn->prepare("DELETE FROM prodotti WHERE id=?");
        $stmt->execute([$id]);
    }
    header('Location: gestisci_prodotti.php');
    exit();
}

if ($action === 'add' || $action === 'edit') {
    $id = $_GET['id'] ?? '';
    $prodotto = [
        'id' => '',
        'nome' => '',
        'descrizione' => '',
        'prezzo' => '',
        'categoria' => '',
        'immagine' => ''
    ];
    if ($action === 'edit' && $id) {
        $stmt = $conn->prepare("SELECT * FROM prodotti WHERE id=?");
        $stmt->execute([$id]);
        $prodotto = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$prodotto) {
            echo "Prodotto non trovato.";
            exit();
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8" />
        <title><?= $action === 'add' ? 'Aggiungi' : 'Modifica' ?> Prodotto</title>
    </head>
    <body>
        <h2><?= $action === 'add' ? 'Aggiungi' : 'Modifica' ?> Prodotto</h2>
        <form method="post" enctype="multipart/form-data" action="gestisci_prodotti.php?action=save">
            <input type="hidden" name="id" value="<?= e($prodotto['id']) ?>" />
            <p>
                <label>Nome:<br />
                <input type="text" name="nome" required value="<?= e($prodotto['nome']) ?>" />
                </label>
            </p>
            <p>
                <label>Descrizione:<br />
                <textarea name="descrizione"><?= e($prodotto['descrizione']) ?></textarea>
                </label>
            </p>
            <p>
                <label>Prezzo:<br />
                <input type="number" name="prezzo" step="0.01" min="0" required value="<?= e($prodotto['prezzo']) ?>" />
                </label>
            </p>
            <p>
                <label>Categoria:<br />
                <input type="text" name="categoria" value="<?= e($prodotto['categoria']) ?>" />
                </label>
            </p>
            <p>
                <label>Immagine: 
                    <?php if ($prodotto['immagine'] && file_exists('../uploads/' . $prodotto['immagine'])): ?>
                        <br /><img src="../uploads/<?= e($prodotto['immagine']) ?>" alt="immagine prodotto" style="max-height:100px;" /><br />
                        <label><input type="checkbox" name="elimina_immagine" /> Elimina immagine attuale</label><br />
                        Lascia vuoto per mantenere l'immagine attuale.
                    <?php endif; ?>
                <br />
                <input type="file" name="immagine" accept="image/*" />
                </label>
            </p>
            <p>
                <button type="submit">Salva</button> | <a href="gestisci_prodotti.php">Annulla</a>
            </p>
        </form>
    </body>
    </html>
    <?php
    exit();
}

$prodotti = $conn->query("SELECT * FROM prodotti ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Gestione Prodotti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .img-prodotto-admin {
            max-height: 50px;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="card-title text-primary mb-0"><i class="bi bi-box-seam"></i> Gestione Prodotti</h2>
                            <a href="gestisci_prodotti.php?action=add" class="btn btn-success"><i class="bi bi-plus-circle"></i> Aggiungi Nuovo Prodotto</a>
                        </div>
                        <!-- Responsive: tabella su desktop, card su mobile -->
                        <div class="d-none d-md-block table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Immagine</th>
                                        <th>Nome</th>
                                        <th>Descrizione</th>
                                        <th>Prezzo</th>
                                        <th>Categoria</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prodotti as $p): ?>
                                        <tr>
                                            <td>
                                                <?php if ($p['immagine'] && file_exists('../uploads/' . $p['immagine'])): ?>
                                                    <img src="../uploads/<?= e($p['immagine']) ?>" alt="<?= e($p['nome']) ?>" class="img-prodotto-admin" />
                                                <?php else: ?>
                                                    <span class="text-muted">(nessuna)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($p['nome']) ?></td>
                                            <td><?= e($p['descrizione']) ?></td>
                                            <td><strong>€ <?= number_format($p['prezzo'], 2) ?></strong></td>
                                            <td><?= e($p['categoria']) ?></td>
                                            <td>
                                                <a href="gestisci_prodotti.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary mb-1"><i class="bi bi-pencil"></i> Modifica</a>
                                                <a href="gestisci_prodotti.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Eliminare prodotto?')"><i class="bi bi-trash"></i> Elimina</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-block d-md-none">
                            <div class="row g-3">
                                <?php foreach ($prodotti as $p): ?>
                                    <div class="col-12">
                                        <div class="card border shadow-sm">
                                            <div class="card-body d-flex align-items-center gap-3">
                                                <?php if ($p['immagine'] && file_exists('../uploads/' . $p['immagine'])): ?>
                                                    <img src="../uploads/<?= e($p['immagine']) ?>" alt="<?= e($p['nome']) ?>" class="img-prodotto-admin flex-shrink-0" style="max-width:70px;" />
                                                <?php else: ?>
                                                    <span class="text-muted">(nessuna)</span>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold text-primary mb-1"><?= e($p['nome']) ?></div>
                                                    <div class="small mb-1 text-muted"><?= e($p['descrizione']) ?></div>
                                                    <div class="mb-1"><span class="badge bg-info-subtle text-dark"><?= e($p['categoria']) ?></span></div>
                                                    <div class="fw-bold text-success mb-2">€ <?= number_format($p['prezzo'], 2) ?></div>
                                                    <div class="d-flex gap-2">
                                                        <a href="gestisci_prodotti.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                        <a href="gestisci_prodotti.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminare prodotto?')"><i class="bi bi-trash"></i></a>
                                                    </div>
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
