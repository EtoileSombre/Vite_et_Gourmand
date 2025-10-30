<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Session;

/**
 * Middleware administrateur
 * Vérifie si l'utilisateur est connecté ET a le rôle admin
 */
class AdminMiddleware
{
    /**
     * Gère la requête
     * 
     * @param Request $request
     * @return bool True si autorisé, False sinon
     */
    public function handle(Request $request): bool
    {
        Session::start();
        
        // Vérifier si l'utilisateur est connecté
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
        
        // Vérifier si l'utilisateur a le rôle admin
        $userRole = Session::get('user_role');
        if ($userRole !== 'admin') {
            http_response_code(403);
            echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>403 - Accès refusé</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { font-size: 72px; color: #e74c3c; }
        p { font-size: 24px; color: #7f8c8d; }
        a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1>403</h1>
    <p>Accès refusé - Vous n'avez pas les permissions nécessaires</p>
    <a href='/'>Retour à l'accueil</a>
</body>
</html>";
            exit;
        }
        
        return true;
    }
}
