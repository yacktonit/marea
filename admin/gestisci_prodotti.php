<?php
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}

require '../includes/db.php';

$action = $_GET['action'] ?? '';

// Funzione per escape output
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Salva o aggiorna prodotto
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $prezzo = floatval($_POST['prezzo'] ?? 0);
    $categoria = trim($_POST['categoria'] ?? '');
    
    // Gestione immagine upload
    $immagine = '';
    $uploadDir = '../uploads/';
    if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['immagine']['tmp_name'];
        $fileName = basename($_FILES['immagine']['name']);
        $targetPath = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $fileName);
        if (move_uploaded_file($tmpName, $targetPath)) {
            $immagine = basename($targetPath);
        }
    }

    if ($id) {
        // Aggiorna
        if ($immagine) {
            $stmt = $conn->prepare("UPDATE prodotti SET nome=?, descrizione=?, prezzo=?, categoria=?, immagine=? WHERE id=?");
            $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $immagine, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE prodotti SET nome=?, descrizione=?, prezzo=?, categoria=? WHERE id=?");
            $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $id]);
        }
    } else {
        // Inserisci nuovo
        $stmt = $conn->prepare("INSERT INTO prodotti (nome, descrizione, prezzo, categoria, immagine) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descrizione, $prezzo, $categoria, $immagine]);
    }

    header('Location: gestisci_prodotti.php');
    exit();
}

if ($action === 'delete') {
    // Elimina prodotto
    $id = $_GET['id'] ?? '';
    if ($id) {
        // Cancella immagine file se esiste
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
    // Mostra form
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

// Lista prodotti (default)
$prodotti = $conn->query("SELECT * FROM prodotti ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Gestione Prodotti</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; text-align: left; }
        img { max-height: 50px; }
    </style>
</head>
<body>
    <h2>Gestione Prodotti</h2>
    <p><a href="gestisci_prodotti.php?action=add">Aggiungi Nuovo Prodotto</a></p>
    <table>
        <thead>
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
                            <img src="../uploads/<?= e($p['immagine']) ?>" alt="<?= e($p['nome']) ?>" />
                        <?php else: ?>
                            (nessuna)
                        <?php endif; ?>
                    </td>
                    <td><?= e($p['nome']) ?></td>
                    <td><?= e($p['descrizione']) ?></td>
                    <td>€ <?= number_format($p['prezzo'], 2) ?></td>
                    <td><?= e($p['categoria']) ?></td>
                    <td>
                        <a href="gestisci_prodotti.php?action=edit&id=<?= $p['id'] ?>">Modifica</a> | 
                        <a href="gestisci_prodotti.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Eliminare prodotto?')">Elimina</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="dashboard.php">Torna alla Dashboard</a></p>
</body>
</html>
