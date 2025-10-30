<?php

namespace App\Core;

/**
 * Classe Request
 * Gère les requêtes HTTP et les données entrantes
 */
class Request
{
    /**
     * Récupère la méthode HTTP de la requête
     * 
     * @return string GET, POST, PUT, DELETE, etc.
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Récupère l'URI de la requête (sans query string)
     * 
     * @return string URI normalisée
     */
    public function getUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    /**
     * Récupère une valeur de $_GET
     * 
     * @param string $key Clé à récupérer
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Récupère une valeur de $_POST
     * 
     * @param string $key Clé à récupérer
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Récupère toutes les données GET et POST
     * 
     * @return array
     */
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Vérifie si la requête est en POST
     * 
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Vérifie si la requête est en GET
     * 
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Vérifie si la requête est en AJAX
     * 
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Récupère l'adresse IP du client
     * 
     * @return string
     */
    public function getIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
