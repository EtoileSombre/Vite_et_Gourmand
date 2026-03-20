<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Factory\RepositoryFactory;
use App\Repository\HoraireRepositoryInterface;

/**
 * Contrôleur Horaire
 * Gestion des horaires d'ouverture par les administrateurs et employés
 */
class HoraireController extends Controller
{
    private HoraireRepositoryInterface $horaireRepository;

    public function __construct()
    {
        parent::__construct();

        // Vérifier que l'utilisateur est connecté et a le rôle employé ou admin
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }

        $userRole = Session::get('user_role');
        if (!in_array($userRole, ['employé', 'administrateur'])) {
            header('Location: /');
            exit;
        }

        $factory = RepositoryFactory::getInstance();
        $this->horaireRepository = $factory->createHoraireRepository();
    }

    /**
     * Affiche la page de gestion des horaires
     */
    public function index()
    {
        // Initialiser les horaires par défaut si la table est vide
        $this->horaireRepository->initializeDefaultHoraires();

        // Récupérer tous les horaires
        $horaires = $this->horaireRepository->findAll();

        $this->render('admin/horaires/index', [
            'title' => 'Gestion des horaires',
            'horaires' => $horaires
        ]);
    }

    /**
     * Met à jour les horaires pour tous les jours de la semaine
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/horaires');
            return;
        }

        if (!csrf_verify()) {
            Session::set('flash_error', 'Erreur de sécurité.');
            $this->redirect('/admin/horaires');
            return;
        }

        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $success = true;

        foreach ($jours as $jour) {
            // Vérifier si le jour est fermé
            $ferme = isset($_POST['ferme_' . $jour]);

            // Récupérer les heures
            $heureOuverture = !empty($_POST['ouverture_' . $jour]) ? $_POST['ouverture_' . $jour] : null;
            $heureFermeture = !empty($_POST['fermeture_' . $jour]) ? $_POST['fermeture_' . $jour] : null;

            // Si fermé, on met les heures à NULL
            if ($ferme) {
                $heureOuverture = null;
                $heureFermeture = null;
            }

            // Validation : si ouvert, les heures doivent être renseignées
            if (!$ferme && (empty($heureOuverture) || empty($heureFermeture))) {
                Session::set('error', "Veuillez renseigner les horaires pour $jour ou cocher 'Fermé'.");
                $this->redirect('/admin/horaires');
                return;
            }

            // Validation : heure de fermeture après heure d'ouverture
            if (!$ferme && $heureOuverture && $heureFermeture && $heureFermeture <= $heureOuverture) {
                Session::set('error', "L'heure de fermeture doit être après l'heure d'ouverture pour $jour.");
                $this->redirect('/admin/horaires');
                return;
            }
            $result = $this->horaireRepository->updateHoraire($jour, $heureOuverture, $heureFermeture, $ferme);

            if (!$result) {
                $success = false;
            }
        }

        if ($success) {
            Session::set('success', 'Les horaires ont été mis à jour avec succès.');
        } else {
            Session::set('error', 'Une erreur est survenue lors de la mise à jour des horaires.');
        }

        $this->redirect('/admin/horaires');
    }
}
