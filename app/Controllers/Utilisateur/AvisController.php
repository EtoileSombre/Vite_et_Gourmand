<?php

namespace App\Controllers\Utilisateur;

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
            Session::set('flash_error', 'Données invalides. Note requise entre 1 et 5 et commentaire obligatoire.');
            $this->redirect('/donner-avis' . ($numeroCommande ? '?commande=' . urlencode($numeroCommande) : ''));
            return;
        }
        
        // Validation longueur minimale du commentaire
        if (strlen(trim($commentaire)) < 10) {
            Session::set('flash_error', 'Votre commentaire doit contenir au moins 10 caractères.');
            $this->redirect('/donner-avis' . ($numeroCommande ? '?commande=' . urlencode($numeroCommande) : ''));
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
            
            Session::set('flash_success', 'Merci ! Votre avis a été enregistré avec succès et sera publié après validation par notre équipe.');
            $this->redirect('/');
        } catch (\Exception $e) {
            error_log("Erreur création avis : " . $e->getMessage());
            Session::set('flash_error', 'Erreur lors de l\'enregistrement de votre avis. Veuillez réessayer.');
            $this->redirect('/donner-avis' . ($numeroCommande ? '?commande=' . urlencode($numeroCommande) : ''));
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
                Session::set('flash_error', 'Commande introuvable.');
                $this->redirect('/mes-commandes');
                return;
            }
            
            // Vérifier que la commande est terminée
            if ($commande['statut'] !== 'terminee') {
                Session::set('flash_error', 'Vous ne pouvez donner un avis que pour une commande terminée.');
                $this->redirect('/mes-commandes');
                return;
            }
            
            // Vérifier qu'un avis n'a pas déjà été donné
            $avisExistant = $this->avisModel->findByCommandeAndUser($numeroCommande, $userId);
            if ($avisExistant) {
                Session::set('flash_error', 'Vous avez déjà donné votre avis pour cette commande.');
                $this->redirect('/mes-commandes');
                return;
            }
        }

        $this->render('utilisateur/avis/create', [
            'numeroCommande' => $numeroCommande
        ]);
    }
}
