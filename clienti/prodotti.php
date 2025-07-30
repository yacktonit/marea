<?php
session_start();
require '../includes/db.php';

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
    <style>
        .categoria-btn {
            margin: 3px;
            padding: 8px 16px;
            border: none;
            background-color: #ddd;
            cursor: pointer;
            border-radius: 4px;
        }
        .categoria-btn.active {
            background-color: #337ab7;
            color: white;
        }
        .prodotto {
            border:1px solid #ccc;
            padding:10px;
            margin:10px 0;
            display: block; /* Mostra tutti i prodotti all'inizio */
        }
    </style>
</head>
<body>
    <h2>Prodotti Disponibili</h2>

    <div id="filtri">
        <?php foreach ($categorie as $cat): ?>
            <button class="categoria-btn" data-cat="<?= htmlspecialchars($cat) ?>">
                <?= htmlspecialchars(ucfirst($cat)) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div id="prodotti-container">
        <?php foreach ($prodotti as $p): ?>
            <div class="prodotto" data-categoria="<?= htmlspecialchars($p['categoria']) ?>">
                <strong><?= htmlspecialchars($p['nome']) ?></strong><br>
                <em><?= htmlspecialchars($p['categoria']) ?></em><br>
                <p><?= htmlspecialchars($p['descrizione']) ?></p>
                <p>Prezzo: € <?= number_format($p['prezzo'], 2) ?></p>
                <form method="post" action="carrello.php">
                    <input type="hidden" name="id_prodotto" value="<?= $p['id'] ?>">
                    <input type="number" name="quantita" value="1" min="1" required>
                    <button type="submit" name="aggiungi">Aggiungi al carrello</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>


    <p><a href="home.php">Torna alla Home</a></p>

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

    // Opzionale: mostra tutti i prodotti e nessun filtro attivo all'inizio
    // Se vuoi che una categoria sia attiva di default, decommenta le righe seguenti:
    // if (btns.length > 0) {
    //     btns[0].classList.add('active');
    //     const cat = btns[0].getAttribute('data-cat');
    //     prodotti.forEach(prod => {
    //         if (prod.getAttribute('data-categoria') === cat) {
    //             prod.style.display = '';
    //         } else {
    //             prod.style.display = 'none';
    //         }
    //     });
    // }
});
</script>

</body>
</html>
