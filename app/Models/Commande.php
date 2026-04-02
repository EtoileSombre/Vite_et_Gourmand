<?php

namespace App\Models;

class Commande
{
    private string $numeroCommande;
    private ?string $dateCommande = null;
    private string $datePrestation;
    private string $heureLivraison;
    private float $prixLivraison = 0.00;
    private float $totalFinal = 0.00;
    private string $lieuLivraison;
    private string $villeLivraison;
    private ?string $codePostalLivraison = null;
    private float $distanceKm = 0.00;
    private ?string $instructionsSpeciales = null;
    private string $statut = 'en_attente';
    private ?string $motifAnnulation = null;
    private bool $pretMateriel = false;
    private ?string $datePretMateriel = null;
    private bool $restitutionMateriel = false;
    private ?string $dateRestitutionMateriel = null;
    private int $utilisateurId;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Champs joints
    private ?string $utilisateurEmail = null;
    private ?string $utilisateurPrenom = null;
    private ?string $utilisateurNom = null;
    private ?string $utilisateurTelephone = null;
    private ?string $utilisateurAdresse = null;
    private ?string $utilisateurVille = null;
    private ?string $utilisateurCodePostal = null;
    private ?string $menuTitre = null;

    // Champs enrichis par les controllers
    private ?array $lignesMenus = null;
    private ?int $totalPersonnes = null;
    private ?float $reductionTotale = null;
    private ?float $sousTotal = null;
    private ?array $lignesMateriels = null;
    private ?float $totalCaution = null;
    private ?array $lignesBoissons = null;
    private ?float $totalBoissons = null;
    private ?float $totalMenus = null;
    private ?string $menuNom = null;
    private ?string $motifModification = null;
    private ?string $modeContactUtilisateur = null;
    private ?string $dateDernierContact = null;

    public const STATUTS = [
        'en_attente' => 'En attente',
        'acceptee' => 'Acceptée',
        'en_preparation' => 'En préparation',
        'en_cours_livraison' => 'En cours de livraison',
        'livree' => 'Livrée',
        'attente_retour_materiel' => 'Attente retour matériel',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
        'refusee' => 'Refusée'
    ];

    public function getNumeroCommande(): string { return $this->numeroCommande; }
    public function setNumeroCommande(string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }

    public function getDateCommande(): ?string { return $this->dateCommande; }
    public function setDateCommande(?string $dateCommande): self { $this->dateCommande = $dateCommande; return $this; }

    public function getDatePrestation(): string { return $this->datePrestation; }
    public function setDatePrestation(string $datePrestation): self { $this->datePrestation = $datePrestation; return $this; }

    public function getHeureLivraison(): string { return $this->heureLivraison; }
    public function setHeureLivraison(string $heureLivraison): self { $this->heureLivraison = $heureLivraison; return $this; }

    public function getPrixLivraison(): float { return $this->prixLivraison; }
    public function setPrixLivraison(float $prixLivraison): self { $this->prixLivraison = $prixLivraison; return $this; }

    public function getTotalFinal(): float { return $this->totalFinal; }
    public function setTotalFinal(float $totalFinal): self { $this->totalFinal = $totalFinal; return $this; }

    public function getLieuLivraison(): string { return $this->lieuLivraison; }
    public function setLieuLivraison(string $lieuLivraison): self { $this->lieuLivraison = $lieuLivraison; return $this; }

    public function getVilleLivraison(): string { return $this->villeLivraison; }
    public function setVilleLivraison(string $villeLivraison): self { $this->villeLivraison = $villeLivraison; return $this; }

    public function getCodePostalLivraison(): ?string { return $this->codePostalLivraison; }
    public function setCodePostalLivraison(?string $codePostalLivraison): self { $this->codePostalLivraison = $codePostalLivraison; return $this; }

    public function getDistanceKm(): float { return $this->distanceKm; }
    public function setDistanceKm(float $distanceKm): self { $this->distanceKm = $distanceKm; return $this; }

    public function getInstructionsSpeciales(): ?string { return $this->instructionsSpeciales; }
    public function setInstructionsSpeciales(?string $instructionsSpeciales): self { $this->instructionsSpeciales = $instructionsSpeciales; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getStatutLabel(): string { return self::STATUTS[$this->statut] ?? $this->statut; }

    public function getMotifAnnulation(): ?string { return $this->motifAnnulation; }
    public function setMotifAnnulation(?string $motifAnnulation): self { $this->motifAnnulation = $motifAnnulation; return $this; }

    public function isPretMateriel(): bool { return $this->pretMateriel; }
    public function setPretMateriel(bool $pretMateriel): self { $this->pretMateriel = $pretMateriel; return $this; }

    public function getDatePretMateriel(): ?string { return $this->datePretMateriel; }
    public function setDatePretMateriel(?string $datePretMateriel): self { $this->datePretMateriel = $datePretMateriel; return $this; }

    public function isRestitutionMateriel(): bool { return $this->restitutionMateriel; }
    public function setRestitutionMateriel(bool $restitutionMateriel): self { $this->restitutionMateriel = $restitutionMateriel; return $this; }

    public function getDateRestitutionMateriel(): ?string { return $this->dateRestitutionMateriel; }
    public function setDateRestitutionMateriel(?string $dateRestitutionMateriel): self { $this->dateRestitutionMateriel = $dateRestitutionMateriel; return $this; }

    public function getUtilisateurId(): int { return $this->utilisateurId; }
    public function setUtilisateurId(int $utilisateurId): self { $this->utilisateurId = $utilisateurId; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getUtilisateurEmail(): ?string { return $this->utilisateurEmail; }
    public function setUtilisateurEmail(?string $utilisateurEmail): self { $this->utilisateurEmail = $utilisateurEmail; return $this; }

    public function getUtilisateurPrenom(): ?string { return $this->utilisateurPrenom; }
    public function setUtilisateurPrenom(?string $utilisateurPrenom): self { $this->utilisateurPrenom = $utilisateurPrenom; return $this; }

    public function getUtilisateurNom(): ?string { return $this->utilisateurNom; }
    public function setUtilisateurNom(?string $utilisateurNom): self { $this->utilisateurNom = $utilisateurNom; return $this; }

    public function getMenuTitre(): ?string { return $this->menuTitre; }
    public function setMenuTitre(?string $menuTitre): self { $this->menuTitre = $menuTitre; return $this; }

    public function getUtilisateurTelephone(): ?string { return $this->utilisateurTelephone; }
    public function setUtilisateurTelephone(?string $utilisateurTelephone): self { $this->utilisateurTelephone = $utilisateurTelephone; return $this; }

    public function getUtilisateurAdresse(): ?string { return $this->utilisateurAdresse; }
    public function setUtilisateurAdresse(?string $utilisateurAdresse): self { $this->utilisateurAdresse = $utilisateurAdresse; return $this; }

    public function getUtilisateurVille(): ?string { return $this->utilisateurVille; }
    public function setUtilisateurVille(?string $utilisateurVille): self { $this->utilisateurVille = $utilisateurVille; return $this; }

    public function getUtilisateurCodePostal(): ?string { return $this->utilisateurCodePostal; }
    public function setUtilisateurCodePostal(?string $utilisateurCodePostal): self { $this->utilisateurCodePostal = $utilisateurCodePostal; return $this; }

    public function getLignesMenus(): ?array { return $this->lignesMenus; }
    public function setLignesMenus(?array $lignesMenus): self { $this->lignesMenus = $lignesMenus; return $this; }

    public function getTotalPersonnes(): ?int { return $this->totalPersonnes; }
    public function setTotalPersonnes(?int $totalPersonnes): self { $this->totalPersonnes = $totalPersonnes; return $this; }

    public function getReductionTotale(): ?float { return $this->reductionTotale; }
    public function setReductionTotale(?float $reductionTotale): self { $this->reductionTotale = $reductionTotale; return $this; }

    public function getSousTotal(): ?float { return $this->sousTotal; }
    public function setSousTotal(?float $sousTotal): self { $this->sousTotal = $sousTotal; return $this; }

    public function getLignesMateriels(): ?array { return $this->lignesMateriels; }
    public function setLignesMateriels(?array $lignesMateriels): self { $this->lignesMateriels = $lignesMateriels; return $this; }

    public function getTotalCaution(): ?float { return $this->totalCaution; }
    public function setTotalCaution(?float $totalCaution): self { $this->totalCaution = $totalCaution; return $this; }

    public function getLignesBoissons(): ?array { return $this->lignesBoissons; }
    public function setLignesBoissons(?array $lignesBoissons): self { $this->lignesBoissons = $lignesBoissons; return $this; }

    public function getTotalBoissons(): ?float { return $this->totalBoissons; }
    public function setTotalBoissons(?float $totalBoissons): self { $this->totalBoissons = $totalBoissons; return $this; }

    public function getTotalMenus(): ?float { return $this->totalMenus; }
    public function setTotalMenus(?float $totalMenus): self { $this->totalMenus = $totalMenus; return $this; }

    public function getMenuNom(): ?string { return $this->menuNom; }
    public function setMenuNom(?string $menuNom): self { $this->menuNom = $menuNom; return $this; }

    public function getMotifModification(): ?string { return $this->motifModification; }
    public function setMotifModification(?string $motifModification): self { $this->motifModification = $motifModification; return $this; }

    public function getModeContactUtilisateur(): ?string { return $this->modeContactUtilisateur; }
    public function setModeContactUtilisateur(?string $modeContactUtilisateur): self { $this->modeContactUtilisateur = $modeContactUtilisateur; return $this; }

    public function getDateDernierContact(): ?string { return $this->dateDernierContact; }
    public function setDateDernierContact(?string $dateDernierContact): self { $this->dateDernierContact = $dateDernierContact; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['numero_commande'])) $entity->setNumeroCommande($data['numero_commande']);
        if (isset($data['date_commande'])) $entity->setDateCommande($data['date_commande']);
        if (isset($data['date_prestation'])) $entity->setDatePrestation($data['date_prestation']);
        if (isset($data['heure_livraison'])) $entity->setHeureLivraison($data['heure_livraison']);
        if (isset($data['prix_livraison'])) $entity->setPrixLivraison((float) $data['prix_livraison']);
        if (isset($data['total_final'])) $entity->setTotalFinal((float) $data['total_final']);
        if (isset($data['lieu_livraison'])) $entity->setLieuLivraison($data['lieu_livraison']);
        if (isset($data['ville_livraison'])) $entity->setVilleLivraison($data['ville_livraison']);
        if (array_key_exists('code_postal_livraison', $data)) $entity->setCodePostalLivraison($data['code_postal_livraison']);
        if (isset($data['distance_km'])) $entity->setDistanceKm((float) $data['distance_km']);
        if (array_key_exists('instructions_speciales', $data)) $entity->setInstructionsSpeciales($data['instructions_speciales']);
        if (isset($data['statut'])) $entity->setStatut($data['statut']);
        if (array_key_exists('motif_annulation', $data)) $entity->setMotifAnnulation($data['motif_annulation']);
        if (isset($data['pret_materiel'])) $entity->setPretMateriel((bool) $data['pret_materiel']);
        if (array_key_exists('date_pret_materiel', $data)) $entity->setDatePretMateriel($data['date_pret_materiel']);
        if (isset($data['restitution_materiel'])) $entity->setRestitutionMateriel((bool) $data['restitution_materiel']);
        if (array_key_exists('date_restitution_materiel', $data)) $entity->setDateRestitutionMateriel($data['date_restitution_materiel']);
        if (isset($data['utilisateur_id'])) $entity->setUtilisateurId((int) $data['utilisateur_id']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        // Champs joints
        if (isset($data['utilisateur_email'])) $entity->setUtilisateurEmail($data['utilisateur_email']);
        if (isset($data['utilisateur_prenom'])) $entity->setUtilisateurPrenom($data['utilisateur_prenom']);
        if (isset($data['utilisateur_nom'])) $entity->setUtilisateurNom($data['utilisateur_nom']);
        if (isset($data['utilisateur_telephone'])) $entity->setUtilisateurTelephone($data['utilisateur_telephone']);
        if (isset($data['utilisateur_adresse'])) $entity->setUtilisateurAdresse($data['utilisateur_adresse']);
        if (isset($data['utilisateur_ville'])) $entity->setUtilisateurVille($data['utilisateur_ville']);
        if (isset($data['utilisateur_code_postal'])) $entity->setUtilisateurCodePostal($data['utilisateur_code_postal']);
        if (isset($data['menu_titre'])) $entity->setMenuTitre($data['menu_titre']);
        if (array_key_exists('motif_modification', $data)) $entity->setMotifModification($data['motif_modification']);
        if (array_key_exists('mode_contact_utilisateur', $data)) $entity->setModeContactUtilisateur($data['mode_contact_utilisateur']);
        if (array_key_exists('date_dernier_contact', $data)) $entity->setDateDernierContact($data['date_dernier_contact']);
        return $entity;
    }
}
