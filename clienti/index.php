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
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mt-5 mb-3">
                <img src="../assets/img/logo.png" alt="Logo Maréa" style="max-width: 120px; height: auto; margin-bottom: 1rem;">
                <h1 class="display-5 fw-bold">Benvenuti in Maréa</h1>
                <br>
                <br>
                <br>
                <br>
            </div>
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="card shadow mt-2">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Accedi al tuo ombrellone!</h2>
                        <?php if (isset($errore)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo htmlspecialchars($errore); ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN</label>
                                <input type="text" class="form-control text-center" id="pin" name="pin" placeholder="Inserisci PIN" required autofocus>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Entra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
