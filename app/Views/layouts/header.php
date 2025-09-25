<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Vite & Gourmand' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/theme-override.css">
    <link rel="stylesheet" href="/assets/css/utilities.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="/">Vite & Gourmand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menus">Menus</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Menu utilisateur connecté -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <strong><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Utilisateur') ?></strong>
                                <span class="badge bg-secondary"><?= htmlspecialchars($_SESSION['user_role'] ?? 'client') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php if (($_SESSION['user_role'] ?? '') === 'administrateur'): ?>
                                    <li><a class="dropdown-item" href="/admin">📊 Dashboard Admin</a></li>
                                    <li><a class="dropdown-item" href="/admin/stats">📈 Statistiques MongoDB</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif (($_SESSION['user_role'] ?? '') === 'employé'): ?>
                                    <li><a class="dropdown-item" href="/employe">📋 Dashboard Employé</a></li>
                                    <li><a class="dropdown-item" href="/employe/commandes">📦 Gestion Commandes</a></li>
                                    <li><a class="dropdown-item" href="/employe/avis">⭐ Modération Avis</a></li>
                                    <li><a class="dropdown-item" href="/admin/menus">🍽️ Gestion Menus</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="/mes-commandes">📦 Mes commandes</a></li>
                                    <li><a class="dropdown-item" href="/donner-avis">⭐ Donner un avis</a></li>
                                    <li><a class="dropdown-item" href="/profil">👤 Mon compte</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="/logout">🚪 Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Menu visiteur non connecté -->
                        <li class="nav-item"><a class="nav-link" href="/login">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-outline-danger btn-sm ms-2" href="/register">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php 
        unset($_SESSION['success']);
    endif;
    
    if (isset($_SESSION['error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php 
        unset($_SESSION['error']);
    endif;
    ?>

    <!-- Contenu principal -->
    <main class="flex-grow-1">
