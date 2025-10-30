<?php
/**
 * Middleware d'authentification
 * Fonctions réutilisables pour gérer les sessions et l'accès aux pages
 */

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si un utilisateur est connecté
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Récupère l'ID de l'utilisateur connecté
 * @return int|null
 */
function getUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Récupère le rôle de l'utilisateur connecté
 * @return string|null (client, employe, admin)
 */
function getUserRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Récupère les informations de l'utilisateur connecté
 * @return array|null
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'prenom' => $_SESSION['prenom'] ?? null,
        'nom' => $_SESSION['nom'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

/**
 * Redirige vers la page de login si l'utilisateur n'est pas connecté
 * @param string $redirectUrl URL de redirection après login
 */
function requireLogin(string $redirectUrl = '/login.php'): void
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Vérifie que l'utilisateur a un rôle spécifique
 * @param string|array $allowedRoles Rôle(s) autorisé(s)
 * @param string $redirectUrl URL de redirection si accès refusé
 */
function requireRole($allowedRoles, string $redirectUrl = '/index.php'): void
{
    requireLogin();

    $userRole = getUserRole();
    $allowedRoles = (array) $allowedRoles;

    if (!in_array($userRole, $allowedRoles)) {
        $_SESSION['error'] = "Accès refusé : privilèges insuffisants.";
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique (sans redirection)
 * @param string $role
 * @return bool
 */
function hasRole(string $role): bool
{
    return getUserRole() === $role;
}

/**
 * Connecte un utilisateur (enregistre ses infos en session)
 * @param array $user Données utilisateur (id, email, prenom, nom, role)
 */
function login(array $user): void
{
    session_regenerate_id(true); // Sécurité : nouveau ID de session

    $_SESSION['user_id'] = $user['utilisateur_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['prenom'] = $user['prenom'] ?? '';
    $_SESSION['nom'] = $user['nom'] ?? '';
    $_SESSION['role'] = $user['role_libelle'] ?? 'client';
}

/**
 * Déconnecte l'utilisateur (détruit la session)
 */
function logout(): void
{
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Redirige l'utilisateur vers une page selon son rôle
 * @param string|null $defaultUrl URL par défaut si pas de redirection enregistrée
 */
function redirectByRole(?string $defaultUrl = null): void
{
    // Si une URL de redirection est enregistrée, l'utiliser
    if (!empty($_SESSION['redirect_after_login'])) {
        $url = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        header("Location: $url");
        exit;
    }

    // Sinon, rediriger selon le rôle
    $role = getUserRole();
    
    switch ($role) {
        case 'admin':
            header("Location: /admin/dashboard.php");
            break;
        case 'employe':
            header("Location: /employe/dashboard.php");
            break;
        case 'client':
        default:
            header("Location: " . ($defaultUrl ?? '/index.php'));
            break;
    }
    exit;
}

/**
 * Affiche un message flash et le supprime de la session
 * @param string $key Clé du message (success, error, info, warning)
 * @return string|null
 */
function getFlashMessage(string $key): ?string
{
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

/**
 * Définit un message flash
 * @param string $key Clé du message
 * @param string $message Message à afficher
 */
function setFlashMessage(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}
