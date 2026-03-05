<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Repository\AvisRepositoryInterface;
use App\Factory\RepositoryFactory;

/**
 * Contrôleur Home
 * Gère la page d'accueil
 */
class HomeController extends Controller
{
    private AvisRepositoryInterface $avisRepository;

    public function __construct()
    {
        // Utilisation de la Factory pour créer le repository
        $factory = RepositoryFactory::getInstance();
        $this->avisRepository = $factory->createAvisRepository();
    }

    /**
     * Affiche la page d'accueil
     * 
     * @return void
     */
    public function index(Request $request): void
    {
        // Récupérer les avis validés (note >= 4) via le repository
        try {
            $avis = $this->avisRepository->findValidatedWithGoodRating(4, 6);
        } catch (\PDOException $e) {
            error_log("Erreur récupération avis : " . $e->getMessage());
            $avis = [];
        }

        $this->render('public/home/index', [
            'title' => 'Accueil - Vite & Gourmand',
            'avis' => $avis
        ]);
    }
}
