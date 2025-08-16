<?php
session_start();
require '../includes/db.php';
require '../includes/template/header_cliente.php';

if (!isset($_SESSION['ombrellone_id'])) {
    header('Location: index.php');
    exit();
}

// Prendi tutte le categorie distinte
$stmt = $conn->query("SELECT DISTINCT categoria FROM prodotti ORDER BY categoria");
$categorie = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Prendi tutti i prodotti ordinati per categoria e nome
$stmt = $conn->query("SELECT * FROM prodotti ORDER BY categoria, nome");
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prodotti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .categoria-btn {
            margin: 3px;
        }
        .categoria-btn.active,
        .categoria-btn:focus {
            background: linear-gradient(135deg, #0077b6, #023e8a) !important;
            color: #fff !important;
            border: none !important;
        }
        .card-prodotto {
            min-height: 100%;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: transform 0.15s;
        }
        .card-prodotto:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 4px 16px rgba(0,119,182,0.10);
        }
        .img-prodotto {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .card-title {
            color: #0077b6;
        }
        .card-category {
            font-size: 0.95rem;
            color: #0096c7;
        }
    </style>
</head>
<body>
    <br>

    <div class="container py-3">
        <h2 class="text-center text-primary mb-4">Prodotti Disponibili</h2>
        <div id="filtri" class="text-center mb-4">
            <?php foreach ($categorie as $cat): ?>
                <button class="categoria-btn btn btn-outline-primary btn-sm" data-cat="<?= htmlspecialchars($cat) ?>">
                    <?= htmlspecialchars(ucfirst($cat)) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div id="prodotti-container" class="row g-4 justify-content-center">
            <?php foreach ($prodotti as $p): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 prodotto" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
                    <div class="card card-prodotto h-100 text-center p-3">
                        <?php if (!empty($p['immagine'])): ?>
                            <img src="<?= htmlspecialchars($p['immagine']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" class="img-prodotto mx-auto">
                        <?php endif; ?>
                        <h5 class="card-title mt-2 mb-1"><?= htmlspecialchars($p['nome']) ?></h5>
                        <div class="card-category mb-2"><i class="bi bi-tag"></i> <?= htmlspecialchars($p['categoria']) ?></div>
                        <p class="card-text small mb-2"><?= htmlspecialchars($p['descrizione']) ?></p>
                        <div class="mb-2 fw-bold text-success">€ <?= number_format($p['prezzo'], 2) ?></div>
                        <form method="post" action="carrello.php" class="d-flex flex-column align-items-center gap-2">
                            <input type="hidden" name="id_prodotto" value="<?= $p['id'] ?>">
                            <input type="number" name="quantita" value="1" min="1" required class="form-control form-control-sm text-center" style="width: 70px;">
                            <button type="submit" name="aggiungi" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-cart-plus"></i> Aggiungi al carrello
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-link text-decoration-none">
                <i class="bi bi-arrow-left"></i> Torna alla Home
            </a>
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const btns = document.querySelectorAll('.categoria-btn');
    const prodotti = document.querySelectorAll('.prodotto');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cat = btn.getAttribute('data-cat');

            prodotti.forEach(prod => {
                if (prod.getAttribute('data-categoria') === cat) {
                    prod.style.display = '';
                } else {
                    prod.style.display = 'none';
                }
            });
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
