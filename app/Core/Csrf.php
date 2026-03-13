<?php

namespace App\Core;

/**
 * Classe Csrf
 * Gestion de la protection CSRF (Cross-Site Request Forgery)
 */
class Csrf
{
    /**
     * Génère un token CSRF et le stocke en session
     */
    public static function generateToken(): string
    {
        Session::start();
        
        // Générer un token aléatoire sécurisé
        $token = bin2hex(random_bytes(32));
        
        // Stocker le token en session
        Session::set('csrf_token', $token);
        
        return $token;
    }

    /**
     * Récupère le token CSRF actuel (ou en génère un nouveau)
     */
    public static function getToken(): string
    {
        Session::start();
        
        if (!Session::has('csrf_token')) {
            return self::generateToken();
        }
        
        return Session::get('csrf_token');
    }

    /**
     * Vérifie la validité du token CSRF
     */
    public static function validateToken(?string $token): bool
    {
        Session::start();
        
        if (empty($token)) {
            return false;
        }
        
        $sessionToken = Session::get('csrf_token');
        
        if (empty($sessionToken)) {
            return false;
        }
        
        // Comparaison sécurisée contre les attaques timing
        return hash_equals($sessionToken, $token);
    }

    /**
     * Génère un champ input hidden avec le token CSRF
     */
    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Vérifie le token CSRF depuis une requête POST
     */
    public static function verify(): bool
    {
        $token = $_POST['csrf_token'] ?? null;
        
        if (!self::validateToken($token)) {
            // Régénérer un nouveau token pour éviter les attaques par rejeu
            self::generateToken();
            return false;
        }
        
        return true;
    }
}
