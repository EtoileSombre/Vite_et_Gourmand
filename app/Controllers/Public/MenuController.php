<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Repository\MenuRepositoryInterface;
use App\Repository\BoissonRepositoryInterface;
use App\Repository\MaterielRepositoryInterface;
use App\Factory\RepositoryFactory;
use App\MongoDB\MongoStats;

class MenuController extends Controller
{
    private MenuRepositoryInterface $menuRepository;
    private BoissonRepositoryInterface $boissonRepository;
    private MaterielRepositoryInterface $materielRepository;

    public function __construct()
    {
        // Utilisation de la Factory pour créer le repository
        $factory = RepositoryFactory::getInstance();
        $this->menuRepository = $factory->createMenuRepository();
        $this->boissonRepository = $factory->createBoissonRepository();
        $this->materielRepository = $factory->createMaterielRepository();
    }

    // Liste des menus disponibles
    public function index(Request $request): void
    {
        $menus = $this->menuRepository->findActiveWithPhotos();
        $themes = $this->menuRepository->getAllThemes();
        $regimes = $this->menuRepository->getAllRegimes();

        // Log MongoDB
        $mongoStats = new MongoStats();
        $mongoStats->logUserActivity('view_menus_list', Session::get('user_id'), [
            'count' => count($menus)
        ]);

        $this->render('public/menus/index', [
            'menus' => $menus,
            'themes' => $themes,
            'regimes' => $regimes,
            'title' => 'Nos Menus'
        ]);
    }

    // Détail d'un menu
    public function show(Request $request): void
    {
        $id = $request->get('id');

        if (!$id) {
            Session::set('error', 'Identifiant de menu manquant');
            $this->redirect('/menus');
            return;
        }

        $menu = $this->menuRepository->findActiveById((int)$id);

        if (!$menu) {
            Session::set('error', 'Menu introuvable ou indisponible');
            $this->redirect('/menus');
            return;
        }

        $boissons = $this->boissonRepository->findAllAvailable();
        $materiels = $this->materielRepository->findAllAvailable();
        $photos = $this->menuRepository->getPhotosMenu((int)$id);

        // Log MongoDB
        $mongoStats = new MongoStats();
        $mongoStats->logMenuView((int)$id, ['titre' => $menu['titre']]);

        // Afficher la vue
        $this->render('public/menus/show', [
            'menu' => $menu,
            'boissons' => $boissons,
            'materiels' => $materiels,
            'photos' => $photos,
            'title' => $menu['titre']
        ]);
    }

    /**
     * API pour filtrer les menus de manière asynchrone
     */
    public function apiFilter(Request $request): void
    {
        try {
            $filters = [];
            
            $regime = $request->get('regime');
            if ($regime && trim($regime) !== '') {
                $filters['regime'] = trim($regime);
            }
            
            $theme = $request->get('theme');
            if ($theme && trim($theme) !== '') {
                $filters['theme'] = trim($theme);
            }
            
            $minPersonnes = $request->get('minPersonnes');
            if ($minPersonnes && is_numeric($minPersonnes) && $minPersonnes > 0) {
                $filters['minPersonnes'] = (int)$minPersonnes;
            }
            
            $prixMin = $request->get('prixMin');
            if ($prixMin && is_numeric($prixMin) && $prixMin > 0) {
                $filters['prixMin'] = (float)$prixMin;
            }
            
            $prixMax = $request->get('prixMax');
            if ($prixMax && is_numeric($prixMax) && $prixMax > 0) {
                $filters['prixMax'] = (float)$prixMax;
            }

            $menus = $this->menuRepository->findFiltered($filters);

            $this->json([
                'success' => true,
                'count' => count($menus),
                'menus' => $menus
            ]);
            
        } catch (\Exception $e) {
            error_log('Erreur dans apiFilter: ' . $e->getMessage());
            
            $this->json([
                'success' => false,
                'error' => 'Une erreur est survenue lors du filtrage'
            ], 500);
        }
    }
}
