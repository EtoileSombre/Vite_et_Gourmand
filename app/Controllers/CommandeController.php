<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Commande;
use App\Models\Menu;
use App\Core\Request;
use App\Core\Session;
use App\Helpers\MongoLogger;

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
        $heureLivraison = $request->post('heure_livraison');
        $adresseLivraison = $request->post('adresse_livraison');
        $lieuLivraison = $request->post('lieu_livraison');
        $distanceKm = floatval($request->post('distance_km'));
        $pretMateriel = $request->post('pret_materiel') ? 1 : 0;

        // Générer un numéro de commande unique
        $numeroCommande = 'CMD' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . uniqid();

        // Récupérer les infos du menu pour les calculs
        $menuModel = new Menu();
        $menu = $menuModel->findById($menuId);
        
        if (!$menu) {
            Session::set('error', 'Menu introuvable.');
            $this->redirect('/commande/nouvelle');
        }

        // ==========================================
        // CALCULS AUTOMATIQUES
        // ==========================================

        // 1. Prix de base du menu
        $prixMenu = $menu['prix_par_personne'] * $nombrePersonnes;

        // 2. Calcul de la réduction de 10% si +5 personnes par rapport au minimum
        $reductionAppliquee = 0;
        $nombrePersonneMinimum = $menu['nombre_personne_minimum'];
        if ($nombrePersonnes >= ($nombrePersonneMinimum + 5)) {
            $reductionAppliquee = $prixMenu * 0.10; // 10% de réduction
        }

        // 3. Calcul des frais de livraison
        // 5€ forfait + 0,59€/km
        $fraisLivraison = 5.00 + ($distanceKm * 0.59);

        // 4. Prix total = Prix menu - Réduction + Frais de livraison
        $prixTotal = $prixMenu - $reductionAppliquee + $fraisLivraison;

        // ==========================================
        // CRÉATION DE LA COMMANDE
        // ==========================================
        
        $commandeModel = new Commande();
        $commandeModel->create([
            'numero_commande' => $numeroCommande,
            'utilisateur_id' => $userId,
            'menu_id' => $menuId,
            'nombre_personne' => $nombrePersonnes,
            'date_commande' => date('Y-m-d'),
            'date_prestation' => $dateLivraison,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $lieuLivraison,
            'adresse_livraison' => $adresseLivraison,
            'distance_km' => $distanceKm,
            'prix_menu' => $prixMenu,
            'frais_livraison' => $fraisLivraison,
            'reduction_appliquee' => $reductionAppliquee,
            'prix_total' => $prixTotal,
            'pret_materiel' => $pretMateriel,
            'statut' => 'en attente'
        ]);

        // ==========================================
        // ENVOI EMAIL DE CONFIRMATION
        // ==========================================
        
        $userEmail = Session::get('user_email');
        $userPrenom = Session::get('user_prenom');
        
        if ($userEmail && $userPrenom && $menu) {
            require_once __DIR__ . '/../config/mail.php';
            
            $detailsCommande = [
                'menu_nom' => $menu['titre'],
                'nombre_personne' => $nombrePersonnes,
                'date_prestation' => $dateLivraison,
                'heure_livraison' => $heureLivraison,
                'adresse_livraison' => $adresseLivraison,
                'prix_par_personne' => $menu['prix_par_personne'],
                'prix_menu' => $prixMenu,
                'reduction' => $reductionAppliquee,
                'frais_livraison' => $fraisLivraison,
                'prix_total' => $prixTotal,
                'pret_materiel' => $pretMateriel
            ];
            
            sendOrderConfirmationEmail($userEmail, $userPrenom, $numeroCommande, $detailsCommande);
        }

        // ==========================================
        // LOGGING MONGODB
        // ==========================================
        
        MongoLogger::logCommande($numeroCommande, $userId, $menuId, $nombrePersonnes, $prixTotal);

        // Message de succès avec détails
        $message = 'Votre commande a été enregistrée avec succès ! Un email de confirmation vous a été envoyé.';
        if ($reductionAppliquee > 0) {
            $message .= sprintf(' Une réduction de 10%% (%.2f €) a été appliquée !', $reductionAppliquee);
        }
        
        Session::set('success', $message);
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
