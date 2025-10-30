<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Database;

/**
 * Contrôleur Home
 * Gère la page d'accueil
 */
class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     * 
     * @param Request $request
     * @return void
     */
    public function index(Request $request): void
    {
        // Récupérer les avis validés (note >= 4)
        $db = Database::getInstance();
        try {
            $stmt = $db->query("
                SELECT a.note, a.description, a.created_at,
                       u.prenom, u.nom
                FROM avis a
                INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                WHERE (a.statut = 'validé' OR a.statut LIKE 'valid%') AND a.note >= 4
                ORDER BY a.created_at DESC
                LIMIT 6
            ");
            $avis = $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Erreur récupération avis : " . $e->getMessage());
            $avis = [];
        }

        $this->render('home/index', [
            'title' => 'Accueil - Vite & Gourmand',
            'avis' => $avis
        ]);
    }
}
