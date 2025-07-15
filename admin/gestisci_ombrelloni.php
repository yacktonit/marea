<?php
session_start();
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
    <style>
        table { border-collapse: collapse; width: 50%; margin-bottom: 1em;}
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center;}
        form { margin-bottom: 2em; }
        .msg-success { color: green; }
        .msg-error { color: red; }
        input[type="text"] { width: 100px; }
        button { padding: 6px 12px; margin: 2px; }
        .actions form { display: inline; }
    </style>
</head>
<body>
    <h2>Gestisci Ombrelloni</h2>

    <?php if ($messaggio): ?>
        <p class="msg-success"><?= e($messaggio) ?></p>
    <?php endif; ?>
    <?php if ($errore): ?>
        <p class="msg-error"><?= e($errore) ?></p>
    <?php endif; ?>

    <table>
        <thead>
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
                        <a href="?edit=<?= e($o['id']) ?>">Modifica</a>
                        <form method="post" onsubmit="return confirm('Eliminare ombrellone #<?= e($o['numero']) ?>?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= e($o['id']) ?>">
                            <button type="submit" name="elimina" style="color:white; background-color:red; border:none; border-radius:4px;">Elimina</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3><?= $edit_ombrellone ? "Modifica Ombrellone #".e($edit_ombrellone['id']) : "Aggiungi Nuovo Ombrellone" ?></h3>
    <form method="post">
        <input type="hidden" name="id" value="<?= e($edit_ombrellone['id'] ?? '') ?>">
        <label>Numero: <input type="text" name="numero" value="<?= e($edit_ombrellone['numero'] ?? '') ?>" required></label><br><br>
        <label>Fila: <input type="text" name="fila" value="<?= e($edit_ombrellone['fila'] ?? '') ?>" required></label><br><br>
        <button type="submit" name="salva"><?= $edit_ombrellone ? "Aggiorna" : "Aggiungi" ?></button>
        <?php if ($edit_ombrellone): ?>
            <a href="gestisci_ombrelloni.php">Annulla</a>
        <?php endif; ?>
    </form>

    <p><a href="dashboard.php">Torna alla Dashboard</a></p>
</body>
</html>
