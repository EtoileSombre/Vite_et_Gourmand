<?php

namespace App\Models;

class Boisson
{
    private ?int $boissonId = null;
    private string $nom;
    private ?string $description = null;
    private string $typeBoisson = 'Autre';
    private float $prixUnitaire;
    private ?string $contenance = null;
    private bool $disponible = true;
    private ?string $photo = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getBoissonId(): ?int { return $this->boissonId; }
    public function setBoissonId(int $boissonId): self { $this->boissonId = $boissonId; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getTypeBoisson(): string { return $this->typeBoisson; }
    public function setTypeBoisson(string $typeBoisson): self { $this->typeBoisson = $typeBoisson; return $this; }

    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): self { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getContenance(): ?string { return $this->contenance; }
    public function setContenance(?string $contenance): self { $this->contenance = $contenance; return $this; }

    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $disponible): self { $this->disponible = $disponible; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['boisson_id'])) $entity->setBoissonId((int) $data['boisson_id']);
        if (isset($data['nom'])) $entity->setNom($data['nom']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description']);
        if (isset($data['type_boisson'])) $entity->setTypeBoisson($data['type_boisson']);
        if (isset($data['prix_unitaire'])) $entity->setPrixUnitaire((float) $data['prix_unitaire']);
        if (array_key_exists('contenance', $data)) $entity->setContenance($data['contenance']);
        if (isset($data['disponible'])) $entity->setDisponible((bool) $data['disponible']);
        if (array_key_exists('photo', $data)) $entity->setPhoto($data['photo']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        return $entity;
    }
}
