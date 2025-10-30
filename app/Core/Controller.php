<?php

namespace App\Core;

/**
 * Classe Controller de base
 * Tous les contrôleurs doivent hériter de cette classe
 */
abstract class Controller
{
    /**
     * Rend une vue avec des données
     * 
     * @param string $view Chemin de la vue (ex: 'menus/index')
     * @param array $data Données à passer à la vue
     * @return void
     */
    protected function render(string $view, array $data = []): void
    {
        // Extraction des données pour les rendre accessibles dans la vue
        extract($data);
        
        // Construction du chemin complet de la vue
        $viewPath = __DIR__ . "/../Views/$view.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("La vue $view n'existe pas");
        }
        
        require_once $viewPath;
    }

    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de redirection
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Retourne une réponse JSON
     * 
     * @param array $data Données à encoder en JSON
     * @param int $statusCode Code HTTP de la réponse
     * @return void
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Retourne une erreur JSON
     * 
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP (par défaut 400)
     * @return void
     */
    protected function jsonError(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }

    /**
     * Vérifie si la requête est en POST
     * 
     * @return bool
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Vérifie si la requête est en GET
     * 
     * @return bool
     */
    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
