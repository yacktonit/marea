<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['pin'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM ombrelloni WHERE pin = ?");
    $stmt->execute([$pin]);
    $ombrellone = $stmt->fetch();

    if ($ombrellone) {
        $_SESSION['ombrellone_id'] = $ombrellone['id'];
        header('Location: home.php');
        exit();
    } else {
        $errore = "PIN non valido.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login Cliente</title></head>
<body>
    <h2>Accesso Ombrellone</h2>
    <form method="post">
        <input type="text" name="pin" placeholder="Inserisci PIN" required>
        <button type="submit">Entra</button>
    </form>
    <?php if (isset($errore)) echo "<p>$errore</p>"; ?>
</body>
</html>
