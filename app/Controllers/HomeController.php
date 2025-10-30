<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Avis;

/**
 * Contrôleur Home
 * Gère la page d'accueil
 */
class HomeController extends Controller
{
    private Avis $avisModel;

    public function __construct()
    {
        $this->avisModel = new Avis();
    }

    /**
     * Affiche la page d'accueil
     * 
     * @return void
     */
    public function index(Request $request): void
    {
        // Récupérer les avis validés (note >= 4) via le modèle
        try {
            $avis = $this->avisModel->findValidatedWithGoodRating(4, 6);
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
