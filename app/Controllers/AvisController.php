<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Avis;

class AvisController extends Controller
{
    private Avis $avisModel;

    public function __construct()
    {
        $this->avisModel = new Avis();
    }

    public function store()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $request = new Request();
        $note = $request->post('note');
        $commentaire = $request->post('commentaire');
        $numeroCommande = $request->post('numero_commande');

        // Validation
        if (!$note || !$commentaire || $note < 1 || $note > 5) {
            Session::set('error', 'Données invalides. Note requise entre 1 et 5.');
            $this->redirect('/avis/create');
            return;
        }

        // Créer l'avis via le modèle
        try {
            $avisData = [
                'utilisateur_id' => $userId,
                'note' => $note,
                'description' => htmlspecialchars($commentaire)
            ];
            
            // Ajouter le numéro de commande si fourni
            if ($numeroCommande) {
                $avisData['numero_commande'] = $numeroCommande;
            }
            
            $avisId = $this->avisModel->createAvis($avisData);
            
            Session::set('success', 'Votre avis a été enregistré et sera publié après validation.');
            $this->redirect('/');
        } catch (\Exception $e) {
            error_log("Erreur création avis : " . $e->getMessage());
            Session::set('error', 'Erreur lors de l\'enregistrement de votre avis.');
            $this->redirect('/donner-avis');
        }
    }

    public function create()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $request = new Request();
        $numeroCommande = $request->get('commande');

        // Si un numéro de commande est fourni, vérifier qu'il appartient à l'utilisateur
        if ($numeroCommande) {
            $commandeModel = new \App\Models\Commande();
            $commande = $commandeModel->findByNumero($numeroCommande);
            
            if (!$commande || $commande['utilisateur_id'] != $userId) {
                Session::set('error', 'Commande introuvable.');
                $this->redirect('/mes-commandes');
                return;
            }
            
            // Vérifier que la commande est terminée
            if ($commande['statut'] !== 'terminee') {
                Session::set('error', 'Vous ne pouvez donner un avis que pour une commande terminée.');
                $this->redirect('/mes-commandes');
                return;
            }
            
            // Vérifier qu'un avis n'a pas déjà été donné
            $avisExistant = $this->avisModel->findByCommandeAndUser($numeroCommande, $userId);
            if ($avisExistant) {
                Session::set('error', 'Vous avez déjà donné votre avis pour cette commande.');
                $this->redirect('/mes-commandes');
                return;
            }
        }

        $this->render('avis/create', [
            'numeroCommande' => $numeroCommande
        ]);
    }
}
