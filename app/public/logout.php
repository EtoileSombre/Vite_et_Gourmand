<?php
/**
 * Déconnexion utilisateur
 * Détruit la session et redirige vers l'accueil
 */

require_once __DIR__ . '/../includes/auth.php';

// Déconnexion
logout();

// Message de confirmation
setFlashMessage('success', 'Vous avez été déconnecté avec succès.');

// Redirection vers l'accueil
header('Location: /index.php');
exit;
