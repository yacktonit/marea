<?php
require '../includes/auth/auth_cliente.php';
require '../includes/template/header_cliente.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media (max-width: 575.98px) {
            .btn-ordina-mobile {
                width: 90vw;
                font-size: 1.1rem;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 mb-5">
                    <div class="card-body text-center">
                        <h2 class="card-title text-primary mb-3"><i class="bi bi-umbrella"></i> Benvenuto nel tuo Ombrellone</h2>
                        <p class="lead">Siamo felici di averti con noi a <span class="fw-semibold text-info">Maréa Beach</span>!</p>
                        <p class="mb-3">Da qui puoi ordinare cibo e bevande direttamente dal tuo ombrellone, visualizzare lo stato dei tuoi ordini e gestire il tuo carrello in modo semplice e veloce.</p>
                        <div class="d-flex flex-column gap-2 align-items-center">
                            <a href="ordini_cliente.php" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-receipt"></i> Visualizza i tuoi ordini
                            </a>
                        </div>
                        <hr class="my-4">
                        <p class="text-muted small mb-0">Hai bisogno di assistenza? Rivolgiti al nostro staff in spiaggia!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bottone Ordina Ora fisso in basso -->
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4 w-100 d-flex justify-content-center" style="z-index: 1050; pointer-events: none;">
        <a href="/marea/clienti/prodotti.php" class="btn btn-success shadow-lg fw-bold btn-ordina-mobile" style="max-width: 400px; pointer-events: auto;">
            <i class="bi bi-bag-plus me-2"></i>Ordina ora
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
