<?php

namespace App\Models;

class Plat
{
    private ?int $platId = null;
    private string $titrePlat;
    private ?string $description = null;
    private string $typePlat = 'Plat';
    private ?string $photo = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Champs joints
    private ?array $allergenes = null;

    public function getPlatId(): ?int { return $this->platId; }
    public function setPlatId(int $platId): self { $this->platId = $platId; return $this; }

    public function getTitrePlat(): string { return $this->titrePlat; }
    public function setTitrePlat(string $titrePlat): self { $this->titrePlat = $titrePlat; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getTypePlat(): string { return $this->typePlat; }
    public function setTypePlat(string $typePlat): self { $this->typePlat = $typePlat; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getAllergenes(): ?array { return $this->allergenes; }
    public function setAllergenes(?array $allergenes): self { $this->allergenes = $allergenes; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['plat_id'])) $entity->setPlatId((int) $data['plat_id']);
        if (isset($data['titre_plat'])) $entity->setTitrePlat($data['titre_plat']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description']);
        if (isset($data['type_plat'])) $entity->setTypePlat($data['type_plat']);
        if (array_key_exists('photo', $data)) $entity->setPhoto($data['photo']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        return $entity;
    }
}
