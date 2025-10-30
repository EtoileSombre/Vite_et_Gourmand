<?php

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Session;

/**
 * Middleware d'authentification
 * Vérifie si l'utilisateur est connecté
 */
class AuthMiddleware
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
            // Sauvegarder l'URL demandée pour redirection après connexion
            Session::set('redirect_after_login', $request->getUri());
            
            // Rediriger vers la page de connexion
            header('Location: /login');
            exit;
        }
        
        return true;
    }
}
