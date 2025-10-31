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

    /**
     * Enregistre un nouvel avis
     */
    public function store()
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $request = new Request();
        $note = $request->post('note');
        $commentaire = $request->post('commentaire');
        $menuId = $request->post('menu_id');

        // Validation
        if (!$note || !$commentaire || $note < 1 || $note > 5) {
            Session::set('error', 'Données invalides. Note requise entre 1 et 5.');
            $this->redirect('/avis/create');
        }

        // Créer l'avis via le modèle
        try {
            $this->avisModel->createAvis([
                'utilisateur_id' => $user['utilisateur_id'],
                'menu_id' => $menuId,
                'note' => $note,
                'description' => htmlspecialchars($commentaire)
            ]);
            
            Session::set('success', 'Votre avis a été enregistré et sera publié après validation.');
            $this->redirect('/');
        } catch (\Exception $e) {
            error_log("Erreur création avis : " . $e->getMessage());
            Session::set('error', 'Erreur lors de l\'enregistrement de votre avis.');
            $this->redirect('/avis/create');
        }
    }

    /**
     * Affiche la page pour donner un avis
     */
    public function create()
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $this->render('avis/create');
    }
}
