<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Menu;
use App\MongoDB\MongoStats;
use App\Repository\BoissonRepositoryInterface;
use App\Repository\CommandeMenuRepositoryInterface;
use App\Repository\CommandeRepositoryInterface;
use App\Repository\MaterielRepositoryInterface;
use App\Repository\MenuRepositoryInterface;
use App\Repository\SuiviCommandeRepositoryInterface;
use App\Services\Exceptions\CommandeException;

class CommandeService extends AbstractService
{
    public const TVA_REDUCTION_SEUIL = 5;
    public const TVA_REDUCTION_TAUX = 0.10;
    public const FRAIS_LIVRAISON_FORFAIT = 5.00;
    public const FRAIS_LIVRAISON_PAR_KM = 0.59;
    public const MATERIEL_DUREE_PRET_JOURS = 10;

    public function __construct(
        private CommandeRepositoryInterface $commandeRepository,
        private MenuRepositoryInterface $menuRepository,
        private CommandeMenuRepositoryInterface $commandeMenuRepository,
        private BoissonRepositoryInterface $boissonRepository,
        private MaterielRepositoryInterface $materielRepository,
        private SuiviCommandeRepositoryInterface $suiviCommandeRepository,
        private MongoStats $mongoStats,
    ) {
    }

    /**
     * Règles : réduction 10% si personnes >= min + 5, livraison = 5€ + 0,59€/km.
     * La caution matériel n'entre pas dans le total.
     */
    public static function calculatePricing(
        float $prixParPersonne,
        int $nombrePersonnes,
        int $nombrePersonneMinimum,
        float $distanceKm,
        float $totalBoissons = 0.0,
    ): array {
        $prixMenu = $prixParPersonne * $nombrePersonnes;

        $reduction = 0.0;
        if ($nombrePersonnes >= ($nombrePersonneMinimum + self::TVA_REDUCTION_SEUIL)) {
            $reduction = $prixMenu * self::TVA_REDUCTION_TAUX;
        }

        $fraisLivraison = self::FRAIS_LIVRAISON_FORFAIT + ($distanceKm * self::FRAIS_LIVRAISON_PAR_KM);

        $totalMenus = $prixMenu - $reduction;
        $prixTotal = $totalMenus + $totalBoissons + $fraisLivraison;

        return [
            'prix_menu' => $prixMenu,
            'reduction' => $reduction,
            'total_menus' => $totalMenus,
            'total_boissons' => $totalBoissons,
            'frais_livraison' => $fraisLivraison,
            'prix_total' => $prixTotal,
        ];
    }

    public static function calculateTotalBoissons(?array $boissons): float
    {
        if (empty($boissons)) {
            return 0.0;
        }
        $total = 0.0;
        foreach ($boissons as $b) {
            $total += ((int) ($b['quantite'] ?? 0)) * ((float) ($b['prix_unitaire'] ?? 0));
        }
        return $total;
    }

    /** Format : C-YYMMDD-XXXX. */
    public static function generateNumeroCommande(): string
    {
        return 'C-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }

    /**
     * @throws CommandeException si données invalides ou menu introuvable
     */
    public function createCommande(int $userId, array $data): array
    {
        $this->requireFields($data, [
            'menu_id',
            'nombre_personnes',
            'date_prestation',
            'heure_livraison',
            'adresse_livraison',
        ]);

        $menuId = (int) $data['menu_id'];
        $nombrePersonnes = (int) $data['nombre_personnes'];
        $dateLivraison = (string) $data['date_prestation'];
        $heureLivraison = (string) $data['heure_livraison'];
        $adresseLivraison = (string) $data['adresse_livraison'];
        $villeLivraison = !empty($data['ville_livraison']) ? (string) $data['ville_livraison'] : 'Bordeaux';
        $codePostalLivraison = (string) ($data['code_postal_livraison'] ?? '');
        $distanceKm = (float) ($data['distance_km'] ?? 0);
        $pretMateriel = !empty($data['pret_materiel']) ? 1 : 0;

        if ($nombrePersonnes <= 0) {
            throw new CommandeException("Le nombre de personnes doit être supérieur à zéro.");
        }

        $menu = $this->menuRepository->findById($menuId);
        if (!$menu) {
            throw new CommandeException("Menu introuvable.");
        }

        $boissonsInput = (!empty($data['boissons']) && is_array($data['boissons'])) ? $data['boissons'] : [];
        $totalBoissons = self::calculateTotalBoissons($boissonsInput);

        $pricing = self::calculatePricing(
            (float) $menu->getPrixParPersonne(),
            $nombrePersonnes,
            (int) $menu->getNombrePersonneMinimum(),
            $distanceKm,
            $totalBoissons,
        );

        $numeroCommande = self::generateNumeroCommande();

        $this->commandeRepository->create([
            'numero_commande' => $numeroCommande,
            'utilisateur_id' => $userId,
            'date_prestation' => $dateLivraison,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $adresseLivraison,
            'ville_livraison' => $villeLivraison,
            'code_postal_livraison' => $codePostalLivraison,
            'distance_km' => $distanceKm,
            'prix_livraison' => $pricing['frais_livraison'],
            'total_final' => $pricing['prix_total'],
            'pret_materiel' => $pretMateriel,
            'statut' => 'en_attente',
        ]);

        $this->commandeMenuRepository->addMenuToCommande(
            $numeroCommande,
            $menuId,
            $nombrePersonnes,
            (float) $menu->getPrixParPersonne(),
            $pricing['reduction'],
        );

        if (!empty($boissonsInput)) {
            try {
                foreach ($boissonsInput as $boisson) {
                    $this->boissonRepository->addBoissonToCommande(
                        $numeroCommande,
                        (int) $boisson['id'],
                        (int) $boisson['quantite'],
                        (float) $boisson['prix_unitaire'],
                    );
                }
            } catch (\Exception $e) {
                error_log("Erreur ajout boissons : " . $e->getMessage());
            }
        }

        if (!empty($data['materiels']) && is_array($data['materiels'])) {
            try {
                $dateRetourPrevue = date(
                    'Y-m-d H:i:s',
                    strtotime($dateLivraison . ' +' . self::MATERIEL_DUREE_PRET_JOURS . ' days'),
                );
                foreach ($data['materiels'] as $materiel) {
                    $this->materielRepository->addMaterielToCommande(
                        $numeroCommande,
                        (int) $materiel['id'],
                        (int) $materiel['quantite'],
                        (float) $materiel['caution_unitaire'],
                        $dateRetourPrevue,
                    );
                    $this->materielRepository->decrementQuantite(
                        (int) $materiel['id'],
                        (int) $materiel['quantite'],
                    );
                }
            } catch (\Exception $e) {
                error_log("Erreur ajout matériel : " . $e->getMessage());
            }
        }

        try {
            $this->mongoStats->logCommande($numeroCommande, [
                'menu_id' => $menuId,
                'prix_total' => $pricing['prix_total'],
                'nombre_personne' => $nombrePersonnes,
                'statut' => 'en_attente',
            ]);
        } catch (\Exception $e) {
            error_log("Erreur log MongoDB commande : " . $e->getMessage());
        }

        error_log(sprintf(
            "[COMMANDE] Création : numero=%s, user_id=%d, menu_id=%d, personnes=%d, total=%s€",
            $numeroCommande,
            $userId,
            $menuId,
            $nombrePersonnes,
            $pricing['prix_total'],
        ));

        return [
            'numero_commande' => $numeroCommande,
            'menu' => $menu,
            'nombre_personnes' => $nombrePersonnes,
            'pricing' => $pricing,
            'date_prestation' => $dateLivraison,
            'heure_livraison' => $heureLivraison,
            'adresse_livraison' => $adresseLivraison,
            'pret_materiel' => $pretMateriel,
        ];
    }

    /**
     * @throws CommandeException
     */
    public function updateCommande(int $userId, array $data): array
    {
        $this->requireFields($data, ['numero_commande', 'nombre_personnes', 'date_prestation']);

        $numeroCommande = (string) $data['numero_commande'];
        $nombrePersonnes = (int) $data['nombre_personnes'];
        $dateLivraison = (string) $data['date_prestation'];

        if ($nombrePersonnes <= 0) {
            throw new CommandeException("Le nombre de personnes doit être supérieur à zéro.");
        }

        $commande = $this->commandeRepository->findByNumero($numeroCommande);
        if (!$commande || $commande->getUtilisateurId() != $userId) {
            throw new CommandeException("Commande introuvable.");
        }

        if ($commande->getStatut() !== 'en_attente') {
            throw new CommandeException(
                "Cette commande ne peut plus être modifiée car elle a été acceptée."
            );
        }

        $lignesMenus = $this->commandeMenuRepository->findByCommande($numeroCommande);

        $this->commandeRepository->updateByNumero($numeroCommande, [
            'date_prestation' => $dateLivraison,
        ]);

        if (!empty($lignesMenus) && $nombrePersonnes != $lignesMenus[0]->getNombrePersonne()) {
            $this->commandeMenuRepository->updateLigne(
                $lignesMenus[0]->getCommandeMenuId(),
                $nombrePersonnes,
                $lignesMenus[0]->getPrixParPersonne(),
                $lignesMenus[0]->getReduction()
            );

            $totalMenus = $this->commandeMenuRepository->getTotalMenus($numeroCommande);
            $nouveauTotal = $totalMenus + (float) $commande->getPrixLivraison();

            $this->commandeRepository->updateByNumero($numeroCommande, [
                'total_final' => $nouveauTotal,
            ]);
        }

        $lignesMenus = $this->commandeMenuRepository->findByCommande($numeroCommande);

        error_log(sprintf(
            "[COMMANDE] Modification : numero=%s, user_id=%d, date=%s, personnes=%d",
            $numeroCommande,
            $userId,
            $dateLivraison,
            $nombrePersonnes,
        ));

        return [
            'numero_commande' => $numeroCommande,
            'lignes_menus' => $lignesMenus,
            'date_prestation' => $dateLivraison,
        ];
    }

    /**
     * @throws CommandeException
     */
    public function cancelCommandeByUser(int $userId, string $numeroCommande): array
    {
        if ($numeroCommande === '') {
            throw new CommandeException("Numéro de commande manquant.");
        }

        $commande = $this->commandeRepository->findByNumero($numeroCommande);
        if (!$commande || $commande->getUtilisateurId() != $userId) {
            throw new CommandeException("Commande introuvable.");
        }

        if ($commande->getStatut() !== 'en_attente') {
            throw new CommandeException(
                "Cette commande ne peut plus être annulée car elle a déjà été acceptée par notre équipe. Veuillez nous contacter."
            );
        }

        $ancienStatut = $commande->getStatut();

        $this->commandeRepository->updateByNumero($numeroCommande, ['statut' => 'annulee']);

        $this->suiviCommandeRepository->enregistrerChangement(
            $numeroCommande,
            $ancienStatut,
            'annulee',
            $userId,
            null
        );

        error_log(sprintf(
            "[COMMANDE] Annulation : numero=%s, user_id=%d",
            $numeroCommande,
            $userId,
        ));

        return [
            'numero_commande' => $numeroCommande,
            'ancien_statut' => $ancienStatut,
        ];
    }

    private const STATUTS_VERROUILLES = ['terminee', 'annulee', 'refusee'];

    /**
     * @throws CommandeException
     */
    public function changeStatutByEmploye(int $employeId, string $numeroCommande, string $nouveauStatut): array
    {
        if ($numeroCommande === '') {
            throw new CommandeException("Commande introuvable");
        }
        if ($nouveauStatut === '') {
            throw new CommandeException("Le nouveau statut est obligatoire");
        }
        if ($nouveauStatut === 'annulee') {
            throw new CommandeException(
                "Vous devez contacter l'utilisateur en remplissant le formulaire \"Modifier Commande\"."
            );
        }

        $commande = $this->commandeRepository->findByNumero($numeroCommande);
        if (!$commande) {
            throw new CommandeException("Commande introuvable");
        }

        $ancienStatut = $commande->getStatut();

        $updateData = ['statut' => $nouveauStatut];
        if ($nouveauStatut === 'terminee') {
            $updateData['restitution_materiel'] = 1;
        }

        $success = $this->commandeRepository->updateByNumero($numeroCommande, $updateData);
        if (!$success) {
            throw new CommandeException("Erreur lors de la mise à jour du statut");
        }

        $this->suiviCommandeRepository->enregistrerChangement(
            $numeroCommande,
            $ancienStatut,
            $nouveauStatut,
            $employeId,
            null,
        );

        try {
            $this->mongoStats->logUserActivity('change_order_status', $employeId, [
                'numero_commande' => $numeroCommande,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $nouveauStatut,
            ]);
        } catch (\Exception $e) {
            error_log("Erreur log MongoDB changement statut : " . $e->getMessage());
        }

        error_log(sprintf(
            "[EMPLOYE] Changement statut : numero=%s, %s -> %s, employe_id=%d",
            $numeroCommande,
            $ancienStatut,
            $nouveauStatut,
            $employeId,
        ));

        return [
            'numero_commande' => $numeroCommande,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commande' => $commande,
        ];
    }

    /**
     * Mode de contact + motif (>= 10 caractères) obligatoires.
     * Si $data['annuler'] est vrai, passe la commande en annulee.
     *
     * @throws CommandeException
     */
    public function editCommandeByEmploye(int $employeId, string $numeroCommande, array $data): array
    {
        if ($numeroCommande === '') {
            throw new CommandeException("Commande introuvable");
        }

        $modeContact = trim((string) ($data['mode_contact'] ?? ''));
        $motif = trim((string) ($data['motif'] ?? ''));
        $annuler = !empty($data['annuler']);

        $errors = [];
        if ($modeContact === '') {
            $errors[] = "Le mode de contact est obligatoire";
        }
        if (strlen($motif) < 10) {
            $errors[] = "Le motif de modification doit contenir au moins 10 caractères";
        }

        $commande = $this->commandeRepository->findByNumero($numeroCommande);
        if (!$commande) {
            throw new CommandeException("Commande introuvable");
        }
        if (in_array($commande->getStatut(), self::STATUTS_VERROUILLES, true)) {
            throw new CommandeException("Cette commande ne peut plus être modifiée");
        }

        if ($annuler) {
            if (!empty($errors)) {
                throw new CommandeException(implode(' ', $errors));
            }

            $ok = $this->commandeRepository->updateByNumero($numeroCommande, [
                'statut' => 'annulee',
                'motif_annulation' => "[Contact: {$modeContact} - ANNULATION] {$motif}",
            ]);
            if (!$ok) {
                throw new CommandeException("Erreur lors de l'annulation de la commande");
            }

            $this->suiviCommandeRepository->enregistrerChangement(
                $numeroCommande,
                $commande->getStatut(),
                'annulee',
                $employeId,
                $motif,
            );

            try {
                $this->mongoStats->logUserActivity('cancel_order', $employeId, [
                    'numero_commande' => $numeroCommande,
                    'mode_contact' => $modeContact,
                    'motif' => $motif,
                ]);
            } catch (\Exception $e) {
                error_log("Erreur log MongoDB annulation employé : " . $e->getMessage());
            }

            return [
                'numero_commande' => $numeroCommande,
                'annulee' => true,
            ];
        }

        $datePrestation = trim((string) ($data['date_prestation'] ?? ''));
        $heureLivraison = trim((string) ($data['heure_livraison'] ?? ''));
        $lieuLivraison = trim((string) ($data['lieu_livraison'] ?? ''));
        $villeLivraison = trim((string) ($data['ville_livraison'] ?? ''));
        $codePostal = trim((string) ($data['code_postal_livraison'] ?? ''));
        $instructions = trim((string) ($data['instructions_speciales'] ?? ''));

        if ($datePrestation === '') $errors[] = "La date de prestation est obligatoire";
        if ($heureLivraison === '') $errors[] = "L'heure de livraison est obligatoire";
        if ($lieuLivraison === '')  $errors[] = "Le lieu de livraison est obligatoire";
        if ($villeLivraison === '') $errors[] = "La ville est obligatoire";
        if ($codePostal === '')     $errors[] = "Le code postal est obligatoire";

        if (!empty($errors)) {
            throw new CommandeException(implode(' ', $errors));
        }

        $ok = $this->commandeRepository->updateByNumero($numeroCommande, [
            'date_prestation' => $datePrestation,
            'heure_livraison' => $heureLivraison,
            'lieu_livraison' => $lieuLivraison,
            'ville_livraison' => $villeLivraison,
            'code_postal_livraison' => $codePostal,
            'instructions_speciales' => $instructions,
            'motif_annulation' => "[Contact: {$modeContact} - MODIFICATION] {$motif}",
        ]);
        if (!$ok) {
            throw new CommandeException("Erreur lors de la modification de la commande");
        }

        if (!empty($data['quantites_menus']) && is_array($data['quantites_menus'])) {
            foreach ($data['quantites_menus'] as $menuId => $quantite) {
                $this->commandeMenuRepository->updateQuantite(
                    $numeroCommande,
                    (int) $menuId,
                    (int) $quantite,
                );
            }
        }

        $this->suiviCommandeRepository->enregistrerChangement(
            $numeroCommande,
            $commande->getStatut(),
            $commande->getStatut(),
            $employeId,
            $motif,
        );

        try {
            $this->mongoStats->logUserActivity('edit_order', $employeId, [
                'numero_commande' => $numeroCommande,
                'mode_contact' => $modeContact,
                'motif' => $motif,
            ]);
        } catch (\Exception $e) {
            error_log("Erreur log MongoDB modification employé : " . $e->getMessage());
        }

        return [
            'numero_commande' => $numeroCommande,
            'annulee' => false,
        ];
    }
}
