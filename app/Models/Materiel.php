<?php

namespace App\Models;

class Materiel
{
    private ?int $materielId = null;
    private string $nom;
    private ?string $description = null;
    private string $categorie = 'Autre';
    private int $quantiteTotale = 0;
    private int $quantiteDisponible = 0;
    private float $prixCaution = 0.00;
    private ?float $valeurUnitaire = null;
    private ?string $photo = null;
    private bool $actif = true;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getMaterielId(): ?int { return $this->materielId; }
    public function setMaterielId(int $materielId): self { $this->materielId = $materielId; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getCategorie(): string { return $this->categorie; }
    public function setCategorie(string $categorie): self { $this->categorie = $categorie; return $this; }

    public function getQuantiteTotale(): int { return $this->quantiteTotale; }
    public function setQuantiteTotale(int $quantiteTotale): self { $this->quantiteTotale = $quantiteTotale; return $this; }

    public function getQuantiteDisponible(): int { return $this->quantiteDisponible; }
    public function setQuantiteDisponible(int $quantiteDisponible): self { $this->quantiteDisponible = $quantiteDisponible; return $this; }

    public function getPrixCaution(): float { return $this->prixCaution; }
    public function setPrixCaution(float $prixCaution): self { $this->prixCaution = $prixCaution; return $this; }

    public function getValeurUnitaire(): ?float { return $this->valeurUnitaire; }
    public function setValeurUnitaire(?float $valeurUnitaire): self { $this->valeurUnitaire = $valeurUnitaire; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['materiel_id'])) $entity->setMaterielId((int) $data['materiel_id']);
        if (isset($data['nom'])) $entity->setNom($data['nom']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description']);
        if (isset($data['categorie'])) $entity->setCategorie($data['categorie']);
        if (isset($data['quantite_totale'])) $entity->setQuantiteTotale((int) $data['quantite_totale']);
        if (isset($data['quantite_disponible'])) $entity->setQuantiteDisponible((int) $data['quantite_disponible']);
        if (isset($data['prix_caution'])) $entity->setPrixCaution((float) $data['prix_caution']);
        if (array_key_exists('valeur_unitaire', $data)) $entity->setValeurUnitaire($data['valeur_unitaire'] !== null ? (float) $data['valeur_unitaire'] : null);
        if (array_key_exists('photo', $data)) $entity->setPhoto($data['photo']);
        if (isset($data['actif'])) $entity->setActif((bool) $data['actif']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        return $entity;
    }
}
