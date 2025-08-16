<?php
// Titolo pagina Dinamico
$pageTitle = isset($pageTitle) ? $pageTitle : basename($_SERVER['PHP_SELF'], '.php');

$menuItems = isset($menuItems) ? $menuItems : [
    ['label' => 'Home', 'link' => '/marea/admin/dashboard.php'],
    ['label' => 'Ordini', 'link' => '/marea/admin/gestisci_ordini.php'],
    ['label' => 'Prodotti', 'link' => '/marea/admin/gestisci_prodotti.php'],
    ['label' => 'Ombrelloni', 'link' => '/marea/admin/gestisci_ombrelloni.php'],
    ['label' => 'Logout', 'link' => '/marea/admin/logout.php'],
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #023e8a;
            --accent-color: #0096c7;
            --light-blue: #caf0f8;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
            font-size: 1.5rem;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            background-color: var(--accent-color);
            color: white !important;
            transform: translateY(-1px);
        }
        
        .navbar-toggler {
            border: none;
            color: white;
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-water me-2"></i><?php echo htmlspecialchars($pageTitle); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($menuItems as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo htmlspecialchars($item['link']); ?>">
                                <?php
                                // Icone per ogni voce
                                switch(strtolower($item['label'])) {
                                    case 'home':
                                        echo '<i class="bi bi-house-door me-1"></i>';
                                        break;
                                    case 'ordini':
                                        echo '<i class="bi bi-receipt me-1"></i>';
                                        break;
                                    case 'prodotti':
                                        echo '<i class="bi bi-box-seam me-1"></i>';
                                        break;
                                    case 'ombrelloni':
                                        echo '<i class="bi bi-umbrella me-1"></i>';
                                        break;
                                    case 'logout':
                                        echo '<i class="bi bi-box-arrow-right me-1"></i>';
                                        break;
                                    default:
                                        echo '<i class="bi bi-circle me-1"></i>';
                                }
                                echo htmlspecialchars($item['label']);
                                ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
</html>