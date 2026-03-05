<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Repository\ContactRepositoryInterface;
use App\Factory\RepositoryFactory;

/**
 * Contrôleur pour la gestion des messages de contact par les admins
 */
class ContactController extends Controller
{
    private ContactRepositoryInterface $contactRepository;

    public function __construct()
    {
        // Vérifier que l'utilisateur est administrateur
        $role = Session::get('user_role');
        if ($role !== 'administrateur') {
            Session::set('error', 'Accès refusé. Réservé aux administrateurs.');
            header('Location: /');
            exit;
        }

        $factory = RepositoryFactory::getInstance();
        $this->contactRepository = $factory->createContactRepository();
    }

    /**
     * Liste tous les messages de contact
     */
    public function index(Request $request): void
    {
        $statutFiltre = $request->get('statut', 'tous');
        
        // Récupérer les messages selon le filtre
        if ($statutFiltre === 'tous') {
            $messages = $this->contactRepository->findAllContacts();
        } else {
            $messages = $this->contactRepository->findAllContacts($statutFiltre);
        }
        $countNouveau = $this->contactRepository->countByStatut('nouveau');
        $countEnCours = $this->contactRepository->countByStatut('en cours');
        $countTraite = $this->contactRepository->countByStatut('traité');

        $this->render('admin/contacts', [
            'title' => 'Gestion des Messages de Contact',
            'messages' => $messages,
            'statut_filtre' => $statutFiltre,
            'count_nouveau' => $countNouveau,
            'count_en_cours' => $countEnCours,
            'count_traite' => $countTraite
        ]);
    }

    /**
     * Change le statut d'un message
     */
    public function changeStatus(Request $request): void
    {
        $contactId = (int) $request->post('contact_id');
        $nouveauStatut = $request->post('statut');

        if (!in_array($nouveauStatut, ['nouveau', 'en cours', 'traité'])) {
            Session::set('error', 'Statut invalide.');
            header('Location: /admin/contacts');
            exit;
        }

        $success = $this->contactRepository->updateStatut($contactId, $nouveauStatut);

        if ($success) {
            Session::set('success', 'Statut mis à jour avec succès.');
        } else {
            Session::set('error', 'Erreur lors de la mise à jour du statut.');
        }

        header('Location: /admin/contacts');
        exit;
    }

    /**
     * Supprime un message
     */
    public function delete(Request $request): void
    {
        $contactId = (int) $request->post('contact_id');

        $success = $this->contactRepository->delete($contactId);

        if ($success) {
            Session::set('success', 'Message supprimé avec succès.');
        } else {
            Session::set('error', 'Erreur lors de la suppression du message.');
        }

        header('Location: /admin/contacts');
        exit;
    }
}
