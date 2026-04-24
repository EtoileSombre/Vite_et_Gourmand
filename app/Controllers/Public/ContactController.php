<?php

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\EmailSecurity;
use App\Core\Request;
use App\Core\Session;
use App\Factory\ServiceFactory;
use App\Services\ContactService;
use App\Services\Exceptions\ContactException;

class ContactController extends Controller
{
    private ContactService $contactService;

    public function __construct()
    {
        $this->contactService = ServiceFactory::getInstance()->createContactService();
    }

    /**
     * Affiche le formulaire de contact et traite l'envoi.
     * Champs : Email, Titre, Description.
     */
    public function index()
    {
        $errors = [];
        $request = new Request();

        if ($request->isPost()) {
            if (!csrf_verify()) {
                EmailSecurity::logSecurityEvent('csrf_failure', ['form' => 'contact']);
                $this->render('public/contact/index', [
                    'errors' => ["Erreur de sécurité. Veuillez réessayer."],
                ]);
                return;
            }

            try {
                $result = $this->contactService->submit(
                    [
                        'email'       => $request->post('email'),
                        'titre'       => $request->post('titre'),
                        'description' => $request->post('description'),
                        'website'     => $request->post('website'),
                    ],
                    EmailSecurity::getClientIp()
                );
            } catch (ContactException $e) {
                $errors[] = $e->getMessage();
                $this->render('public/contact/index', ['errors' => $errors]);
                return;
            }

            // Succès (réel ou feint pour honeypot)
            Session::set('contact_envoye', true);
            $this->redirect('/contact');
            return;
        }

        $this->render('public/contact/index', [
            'errors' => $errors,
        ]);
    }
}
