<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=marea;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = 'admin';
    $password = 'admin';

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "Utente non trovato.";
        exit;
    }

    if (password_verify($password, $admin['password_hash'])) {
        echo "Password corretta, login OK!";
    } else {
        echo "Password errata.";
    }
} catch (PDOException $e) {
    echo "Errore DB: " . $e->getMessage();
}
