<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Commande;
use App\Models\Menu;
use App\Core\Request;
use App\Core\Session;

class CommandeController extends Controller
{
    /**
     * Liste des commandes de l'utilisateur
     */
    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $commandeModel = new Commande();
        $commandes = $commandeModel->findByUser($userId);
        
        $this->render('commandes/index', ['commandes' => $commandes]);
    }

    /**
     * Formulaire de création de commande
     */
    public function create()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $menuModel = new Menu();
        $menus = $menuModel->findAll();
        
        $this->render('commandes/create', ['menus' => $menus]);
    }

    /**
     * Enregistrement d'une nouvelle commande
     */
    public function store()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $menuId = $request->post('menu_id');
        $nombrePersonnes = $request->post('nombre_personnes');
        $dateLivraison = $request->post('date_livraison');

        // Générer un numéro de commande unique
        $numeroCommande = 'CMD' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . uniqid();

        // Récupérer les infos du menu pour l'email
        $menuModel = new Menu();
        $menu = $menuModel->findById($menuId);
        
        // Créer la commande
        $commandeModel = new Commande();
        $commandeModel->create([
            'numero_commande' => $numeroCommande,
            'utilisateur_id' => $userId,
            'menu_id' => $menuId,
            'nombre_personne' => $nombrePersonnes,
            'date_commande' => date('Y-m-d'),
            'date_prestation' => $dateLivraison,
            'statut' => 'en attente'
        ]);

        // Envoyer l'email de confirmation
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom && $menu) {
            require_once __DIR__ . '/../config/mail.php';
            
            $detailsCommande = [
                'menu_nom' => $menu['titre'],
                'nombre_personne' => $nombrePersonnes,
                'date_prestation' => $dateLivraison,
                'prix_par_personne' => $menu['prix_par_personne']
            ];
            
            sendOrderConfirmationEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        $this->redirect('/mes-commandes');
    }

    /**
     * Formulaire de modification de commande
     */
    public function edit()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->get('numero');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            $this->redirect('/mes-commandes');
        }

        $menuModel = new Menu();
        $menus = $menuModel->findAll();

        $this->render('commandes/edit', [
            'commande' => $commande,
            'menus' => $menus
        ]);
    }

    /**
     * Mise à jour d'une commande
     */
    public function update()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->post('numero_commande');
        $nombrePersonnes = $request->post('nombre_personnes');
        $dateLivraison = $request->post('date_livraison');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            $this->redirect('/mes-commandes');
        }

        // Mettre à jour la commande
        $commandeModel->updateByNumero($numeroCommande, [
            'nombre_personne' => $nombrePersonnes,
            'date_prestation' => $dateLivraison
        ]);

        // Envoyer l'email de modification
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom) {
            require_once __DIR__ . '/../config/mail.php';
            
            $detailsCommande = [
                'menu_nom' => $commande['menu_nom'],
                'nombre_personne' => $nombrePersonnes,
                'date_prestation' => $dateLivraison,
                'prix_par_personne' => $commande['menu_prix']
            ];
            
            sendOrderUpdateEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        $this->redirect('/mes-commandes');
    }

    /**
     * Annulation d'une commande
     */
    public function cancel()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
        }

        $request = new Request();
        $numeroCommande = $request->get('numero');

        $commandeModel = new Commande();
        $commande = $commandeModel->findByNumero($numeroCommande);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $userId) {
            $this->redirect('/mes-commandes');
        }

        // Mettre à jour le statut au lieu de supprimer
        $commandeModel->updateByNumero($numeroCommande, ['statut' => 'annulée']);

        $this->redirect('/mes-commandes');
    }
}
