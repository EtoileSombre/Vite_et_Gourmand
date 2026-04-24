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

/**
 * Service métier pour la gestion des commandes.
 *
 * Centralise la logique de tarification, la création/modification/annulation
 * des commandes, la persistance multi-tables (lignes menus, boissons,
 * matériels), la mise à jour des stocks et le logging MongoDB.
 *
 * Le service est agnostique vis-à-vis de HTTP : aucune Session, Request,
 * header() ni echo. Les controllers orchestrent l'appel et gèrent :
 *   - l'autorisation (utilisateur connecté, rôle)
 *   - l'envoi des emails (à partir du payload retourné)
 *   - les flash messages et redirects
 */
class CommandeService extends AbstractService
{
    public const TVA_REDUCTION_SEUIL = 5;       // nb personnes au-dessus du min
    public const TVA_REDUCTION_TAUX = 0.10;     // 10% de réduction
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

    // ========================================================================
    // LOGIQUE PURE DE TARIFICATION (testable sans repo)
    // ========================================================================

    /**
     * Calcule la tarification complète d'une commande.
     *
     * Règles métier :
     *  - Prix de base = prix_par_personne × nombre_personnes
     *  - Réduction 10% si nombre_personnes >= minimum + 5
     *  - Frais livraison = 5€ forfait + 0,59€/km
     *  - Total TTC = (prix_base − réduction) + total_boissons + frais_livraison
     *
     * La caution matériel n'entre PAS dans le total (restituable).
     *
     * @return array{
     *   prix_menu: float,
     *   reduction: float,
     *   total_menus: float,
     *   total_boissons: float,
     *   frais_livraison: float,
     *   prix_total: float
     * }
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

    /**
     * Calcule le total des boissons à partir du tableau de données en entrée.
     *
     * @param array<int, array{quantite:int|string, prix_unitaire:float|string}>|null $boissons
     */
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

    /**
     * Génère un numéro de commande unique au format C-YYMMDD-XXXX.
     */
    public static function generateNumeroCommande(): string
    {
        return 'C-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }

    // ========================================================================
    // CAS D'USAGE : CRÉATION D'UNE COMMANDE (UTILISATEUR)
    // ========================================================================

    /**
     * Crée une nouvelle commande pour un utilisateur.
     *
     * Orchestration :
     *  1. Validation des champs obligatoires
     *  2. Chargement et vérification du menu
     *  3. Calcul du prix (calculatePricing)
     *  4. Persistance de l'en-tête de commande
     *  5. Ajout de la ligne menu principale
     *  6. Ajout des boissons (si fournies)
     *  7. Ajout des matériels + décrément du stock (si fournis)
     *  8. Log MongoDB
     *
     * L'envoi d'email est laissé au controller, qui utilise le payload retourné.
     *
     * @param array{
     *   menu_id: int|string,
     *   nombre_personnes: int|string,
     *   date_prestation: string,
     *   heure_livraison: string,
     *   adresse_livraison: string,
     *   ville_livraison?: string,
     *   code_postal_livraison?: string,
     *   distance_km?: float|int|string,
     *   pret_materiel?: mixed,
     *   boissons?: array<int, array{id:int|string, quantite:int|string, prix_unitaire:float|string}>,
     *   materiels?: array<int, array{id:int|string, quantite:int|string, caution_unitaire:float|string}>
     * } $data
     *
     * @return array{
     *   numero_commande: string,
     *   menu: Menu,
     *   nombre_personnes: int,
     *   pricing: array{prix_menu:float, reduction:float, total_menus:float, frais_livraison:float, prix_total:float},
     *   date_prestation: string,
     *   heure_livraison: string,
     *   adresse_livraison: string,
     *   pret_materiel: int
     * }
     *
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

        // 1. En-tête de commande
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

        // 2. Ligne menu principale
        $this->commandeMenuRepository->addMenuToCommande(
            $numeroCommande,
            $menuId,
            $nombrePersonnes,
            (float) $menu->getPrixParPersonne(),
            $pricing['reduction'],
        );

        // 3. Boissons (optionnel, non bloquant)
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

        // 4. Matériels + décrément stock (optionnel, non bloquant)
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

        // 5. Logging MongoDB (non bloquant)
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

    // ========================================================================
    // CAS D'USAGE : MODIFICATION D'UNE COMMANDE (UTILISATEUR)
    // ========================================================================

    /**
     * Modifie une commande existante d'un utilisateur.
     *
     * Règles métier :
     *  - La commande doit appartenir à l'utilisateur.
     *  - Elle doit être au statut `en_attente` (sinon refus).
     *  - Si le nombre de personnes change, on met à jour la première ligne
     *    et on recalcule total_final (menus + boissons déjà en base + livraison).
     *
     * @param array{
     *   numero_commande: string,
     *   nombre_personnes: int|string,
     *   date_prestation: string
     * } $data
     *
     * @return array{
     *   numero_commande: string,
     *   lignes_menus: array,
     *   date_prestation: string
     * }
     *
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

        // Si le nombre de personnes a changé, on met à jour la 1ère ligne et on recalcule
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

        // Recharger les lignes après update
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

    // ========================================================================
    // CAS D'USAGE : ANNULATION D'UNE COMMANDE (UTILISATEUR)
    // ========================================================================

    /**
     * Annule une commande d'un utilisateur.
     *
     * Règles métier :
     *  - La commande doit appartenir à l'utilisateur.
     *  - Elle doit être au statut `en_attente` (sinon refus, il faut passer
     *    par le support).
     *  - Change le statut en `annulee` et enregistre dans le suivi.
     *
     * @return array{numero_commande: string, ancien_statut: string}
     *
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
            "Annulation par l'utilisateur"
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

    // ========================================================================
    // CAS D'USAGE : GESTION CÔTÉ EMPLOYÉ
    // ========================================================================

    /**
     * Statuts interdits en modification pour un employé.
     */
    private const STATUTS_VERROUILLES = ['terminee', 'annulee', 'refusee'];

    /**
     * Change le statut d'une commande par un employé.
     *
     * Règles :
     *  - Le statut `annulee` n'est pas autorisé ici (passer par editByEmploye
     *    avec un motif pour garder la traçabilité du contact utilisateur).
     *  - Si le nouveau statut est `terminee`, marque la restitution matériel.
     *  - Enregistre le changement dans le suivi et MongoDB.
     *
     * @return array{numero_commande: string, ancien_statut: string, nouveau_statut: string, commande: Commande}
     *
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
     * Modifie (ou annule) une commande par un employé, avec motif obligatoire.
     *
     * Règles :
     *  - Commande non terminée/annulée/refusée.
     *  - Mode de contact et motif (≥ 10 caractères) obligatoires.
     *  - Si `annuler` = true : passe le statut à `annulee`, sinon met à jour
     *    les champs de livraison + les quantités de menus.
     *
     * @param array{
     *   mode_contact: string,
     *   motif: string,
     *   annuler?: bool,
     *   date_prestation?: string,
     *   heure_livraison?: string,
     *   lieu_livraison?: string,
     *   ville_livraison?: string,
     *   code_postal_livraison?: string,
     *   instructions_speciales?: string,
     *   quantites_menus?: array<int|string, int|string>
     * } $data
     *
     * @return array{numero_commande: string, annulee: bool}
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

        // ---- Branche ANNULATION ----
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
                "[ANNULATION] {$motif}",
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

        // ---- Branche MODIFICATION ----
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
            $commande->getStatut(), // même statut
            $employeId,
            "[MODIFICATION] {$motif}",
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
