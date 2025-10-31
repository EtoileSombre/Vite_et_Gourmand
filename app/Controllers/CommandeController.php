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
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $commandeModel = new Commande();
        $commandes = $commandeModel->findByUser($user['id']);
        
        $this->render('commandes/index', ['commandes' => $commandes]);
    }

    /**
     * Formulaire de création de commande
     */
    public function create()
    {
        $user = Session::get('user');
        if (!$user) {
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
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $request = new Request();
        $menuId = $request->post('menu_id');
        $quantite = $request->post('quantite');
        $dateLivraison = $request->post('date_livraison');

        $commandeModel = new Commande();
        $commandeModel->create([
            'utilisateur_id' => $user['id'],
            'menu_id' => $menuId,
            'quantite' => $quantite,
            'date_livraison' => $dateLivraison,
            'date_commande' => date('Y-m-d H:i:s'),
            'statut' => 'en_attente'
        ]);

        $this->redirect('/mes-commandes');
    }

    /**
     * Formulaire de modification de commande
     */
    public function edit()
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $request = new Request();
        $id = $request->get('id');

        $commandeModel = new Commande();
        $commande = $commandeModel->findWithDetails((int)$id);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $user['id']) {
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
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $request = new Request();
        $id = $request->post('id');
        $quantite = $request->post('quantite');
        $dateLivraison = $request->post('date_livraison');

        $commandeModel = new Commande();
        $commande = $commandeModel->findById((int)$id);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $user['id']) {
            $this->redirect('/mes-commandes');
        }

        $commandeModel->update((int)$id, [
            'quantite' => $quantite,
            'date_livraison' => $dateLivraison
        ]);

        $this->redirect('/mes-commandes');
    }

    /**
     * Annulation d'une commande
     */
    public function cancel()
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $request = new Request();
        $id = $request->get('id');

        $commandeModel = new Commande();
        $commande = $commandeModel->findById((int)$id);

        // Vérifier que la commande appartient à l'utilisateur
        if (!$commande || $commande['utilisateur_id'] != $user['id']) {
            $this->redirect('/mes-commandes');
        }

        // Mettre à jour le statut au lieu de supprimer
        $commandeModel->update((int)$id, ['statut' => 'annulee']);

        $this->redirect('/mes-commandes');
    }
}
