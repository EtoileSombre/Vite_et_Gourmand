<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Vite & Gourmand</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/app.css" rel="stylesheet"> 

 <style>
  body{font-family:'Montserrat',system-ui, -apple-system, Segoe UI, Roboto, sans-serif;}
 </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php
// Charger le middleware d'authentification
require_once __DIR__ . '/auth.php';
$currentUser = getCurrentUser();
?>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold text-danger" href="/index.php">Vite & Gourmand</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="/index.php">Accueil</a></li>
          <li class="nav-item"><a class="nav-link" href="/menus.php">Menus</a></li>
          <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
          
          <?php if (isLoggedIn()): ?>
            <!-- Menu utilisateur connecté -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?= htmlspecialchars($currentUser['prenom'] ?? 'Utilisateur') ?></strong>
                <span class="badge bg-secondary"><?= htmlspecialchars($currentUser['role'] ?? 'client') ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <?php if (hasRole('admin')): ?>
                  <li><a class="dropdown-item" href="/admin/dashboard.php">📊 Dashboard Admin</a></li>
                  <li><hr class="dropdown-divider"></li>
                <?php elseif (hasRole('employe')): ?>
                  <li><a class="dropdown-item" href="/employe/dashboard.php">📋 Dashboard Employé</a></li>
                  <li><hr class="dropdown-divider"></li>
                <?php else: ?>
                  <li><a class="dropdown-item" href="/mes-commandes.php">📦 Mes commandes</a></li>
                  <li><a class="dropdown-item" href="/mon-compte.php">👤 Mon compte</a></li>
                  <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
                <li><a class="dropdown-item text-danger" href="/logout.php">🚪 Déconnexion</a></li>
              </ul>
            </li>
          <?php else: ?>
            <!-- Menu visiteur non connecté -->
            <li class="nav-item"><a class="nav-link" href="/login.php">Connexion</a></li>
            <li class="nav-item"><a class="nav-link btn btn-outline-danger btn-sm ms-2" href="/register.php">S'inscrire</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
