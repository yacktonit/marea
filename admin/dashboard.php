<?php
session_start();
require '../includes/db.php';
require '../includes/template/header_admin.php';
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: login.php');
    exit();
}
// Statistiche
$tot_ordini = $conn->query("SELECT COUNT(*) FROM ordini")->fetchColumn();
$tot_prodotti = $conn->query("SELECT COUNT(*) FROM prodotti")->fetchColumn();
$tot_ombrelloni = $conn->query("SELECT COUNT(*) FROM ombrelloni")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-primary text-center"><i class="bi bi-speedometer2"></i> Dashboard Amministratore</h1>
        <div class="row g-4 justify-content-center mb-5">
            <div class="col-12 col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <div class="mb-2"><i class="bi bi-receipt text-success" style="font-size:2rem;"></i></div>
                        <h5 class="card-title">Ordini totali</h5>
                        <p class="display-5 fw-bold mb-0"><?php echo $tot_ordini; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <div class="mb-2"><i class="bi bi-box-seam text-warning" style="font-size:2rem;"></i></div>
                        <h5 class="card-title">Prodotti totali</h5>
                        <p class="display-5 fw-bold mb-0"><?php echo $tot_prodotti; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow border-0 text-center">
                    <div class="card-body">
                        <div class="mb-2"><i class="bi bi-umbrella text-info" style="font-size:2rem;"></i></div>
                        <h5 class="card-title">Ombrelloni totali</h5>
                        <p class="display-5 fw-bold mb-0"><?php echo $tot_ombrelloni; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
