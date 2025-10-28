<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;

class ContactController extends Controller
{
    /**
     * Affiche le formulaire de contact et traite l'envoi
     */
    public function index()
    {
        $errors = [];
        $success = false;
        $request = new Request();

        if ($request->isPost()) {
            $nom = $request->post('nom');
            $email = $request->post('email');
            $message = $request->post('message');

            // Validation
            $validator = new Validator();

            if (empty($nom)) {
                $errors[] = "Le nom est obligatoire.";
            }

            if (!$validator->email($email)) {
                $errors[] = "L'email n'est pas valide.";
            }

            if (empty($message)) {
                $errors[] = "Le message est obligatoire.";
            }

            // Si pas d'erreurs, envoyer l'email (à implémenter)
            if (empty($errors)) {
                // TODO: Implémenter l'envoi d'email
                // Pour l'instant, juste simuler le succès
                $success = true;
            }
        }

        $this->render('contact/index', [
            'errors' => $errors,
            'success' => $success
        ]);
    }
}
