<?php

namespace App\Core;

/**
 * Classe Router
 * Gère le routage de l'application
 */
class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Ajoute une route GET
     * 
     * @param string $path Chemin de la route
     * @param string $controller Nom complet de la classe du contrôleur
     * @param string $method Méthode du contrôleur à appeler
     * @param array $middlewares Middlewares à exécuter
     * @return void
     */
    public function get(string $path, string $controller, string $method, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $controller, $method, $middlewares);
    }

    /**
     * Ajoute une route POST
     * 
     * @param string $path Chemin de la route
     * @param string $controller Nom complet de la classe du contrôleur
     * @param string $method Méthode du contrôleur à appeler
     * @param array $middlewares Middlewares à exécuter
     * @return void
     */
    public function post(string $path, string $controller, string $method, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $controller, $method, $middlewares);
    }

    /**
     * Ajoute une route au routeur
     * 
     * @param string $httpMethod Méthode HTTP (GET, POST, etc.)
     * @param string $path Chemin de la route
     * @param string $controller Classe du contrôleur
     * @param string $method Méthode du contrôleur
     * @param array $middlewares Middlewares à exécuter
     * @return void
     */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method, array $middlewares): void
    {
        $this->routes[] = [
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'action' => $method,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Dispatche la requête vers le bon contrôleur
     * 
     * @return void
     */
    public function dispatch(): void
    {
        $request = new Request();
        $requestMethod = $request->getMethod();
        
        // Utiliser le paramètre 'url' si présent (pour index_mvc.php?url=/menus)
        // Sinon, si on est sur index_mvc.php sans paramètre, c'est la page d'accueil
        if ($urlParam = $request->get('url')) {
            $requestUri = '/' . ltrim($urlParam, '/');
        } else {
            $currentUri = $request->getUri();
            // Si on accède à index_mvc.php directement, c'est la route '/'
            if ($currentUri === '/index_mvc.php' || $currentUri === '/') {
                $requestUri = '/';
            } else {
                $requestUri = $currentUri;
            }
        }

        // Retirer le basePath de l'URI si défini
        if ($this->basePath && str_starts_with($requestUri, $this->basePath)) {
            $requestUri = substr($requestUri, strlen($this->basePath)) ?: '/';
        }

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestUri)) {
                // Exécuter les middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (!$middleware->handle($request)) {
                        return; // Middleware a bloqué la requête
                    }
                }

                // Instancier le contrôleur et appeler la méthode
                $controller = new $route['controller']();
                $action = $route['action'];
                $controller->$action($request);
                return;
            }
        }

        // Aucune route trouvée - 404
        $this->handleNotFound();
    }

    /**
     * Vérifie si une route correspond à la requête
     * 
     * @param array $route Route à vérifier
     * @param string $requestMethod Méthode HTTP de la requête
     * @param string $requestUri URI de la requête
     * @return bool
     */
    private function matchRoute(array $route, string $requestMethod, string $requestUri): bool
    {
        return $route['method'] === $requestMethod && $route['path'] === $requestUri;
    }

    /**
     * Gère les erreurs 404
     * 
     * @return void
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>404 - Page non trouvée</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { font-size: 72px; color: #e74c3c; }
        p { font-size: 24px; color: #7f8c8d; }
        a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Page non trouvée</p>
    <a href='/'>Retour à l'accueil</a>
</body>
</html>";
        exit;
    }
}
