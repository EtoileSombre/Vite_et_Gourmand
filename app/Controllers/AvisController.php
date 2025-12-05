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
        $menuId = $request->post('menu_id');

        // Validation
        if (!$note || !$commentaire || $note < 1 || $note > 5) {
            Session::set('error', 'Données invalides. Note requise entre 1 et 5.');
            $this->redirect('/donner-avis');
        }

        // Créer l'avis via le modèle
        try {
            $avisId = $this->avisModel->createAvis([
                'utilisateur_id' => $userId,
                'note' => $note,
                'description' => htmlspecialchars($commentaire)
            ]);
            
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

        $this->render('avis/create');
    }
}
