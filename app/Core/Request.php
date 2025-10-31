<?php

namespace App\Core;

/**
 * Classe Request
 * Gère les requêtes HTTP
 */
class Request
{
    // Récupère la méthode HTTP (GET, POST, etc.)
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    // Récupère l'URL demandée
    public function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    // Récupère une valeur de $_GET
    public function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    // Récupère une valeur de $_POST
    public function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    // Vérifie si c'est une requête POST
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }
}
