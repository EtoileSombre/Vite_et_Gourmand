<?php

namespace App\Core;

abstract class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    protected function render($view, $data = [])
    {
        header('Content-Type: text/html; charset=UTF-8');

        // Injecter les horaires formatés pour le footer
        if (!isset($data['horairesFormatted'])) {
            try {
                $horaireRepo = \App\Factory\RepositoryFactory::getInstance()->createHoraireRepository();
                $data['horairesFormatted'] = $horaireRepo->getHorairesFormatted();
            } catch (\Exception $e) {
                $data['horairesFormatted'] = 'Lun–Dim 10h–22h';
            }
        }

        extract($data);
        
        $viewPath = __DIR__ . "/../Views/$view.php";
        
        if (!file_exists($viewPath)) {
            echo "Erreur : La vue $view n'existe pas";
            return;
        }
        
        require_once $viewPath;
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
