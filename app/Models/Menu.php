<?php

namespace App\Models;

class Menu implements \JsonSerializable
{
    private ?int $menuId = null;
    private string $titre;
    private int $nombrePersonneMinimum = 2;
    private float $prixParPersonne;
    private ?string $description = null;
    private ?string $conditions = null;
    private int $quantiteRestante = 0;
    private ?string $imagePrincipale = null;
    private bool $actif = true;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Champs joints
    private ?array $plats = null;
    private ?array $galerie = null;
    private ?string $theme = null;
    private ?string $themeLibelle = null;
    private ?string $regime = null;
    private ?string $allergenes = null;

    public function getMenuId(): ?int { return $this->menuId; }
    public function setMenuId(int $menuId): self { $this->menuId = $menuId; return $this; }

    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getNombrePersonneMinimum(): int { return $this->nombrePersonneMinimum; }
    public function setNombrePersonneMinimum(int $nombrePersonneMinimum): self { $this->nombrePersonneMinimum = $nombrePersonneMinimum; return $this; }

    public function getPrixParPersonne(): float { return $this->prixParPersonne; }
    public function setPrixParPersonne(float $prixParPersonne): self { $this->prixParPersonne = $prixParPersonne; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getConditions(): ?string { return $this->conditions; }
    public function setConditions(?string $conditions): self { $this->conditions = $conditions; return $this; }

    public function getQuantiteRestante(): int { return $this->quantiteRestante; }
    public function setQuantiteRestante(int $quantiteRestante): self { $this->quantiteRestante = $quantiteRestante; return $this; }

    public function getImagePrincipale(): ?string { return $this->imagePrincipale; }
    public function setImagePrincipale(?string $imagePrincipale): self { $this->imagePrincipale = $imagePrincipale; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getPlats(): ?array { return $this->plats; }
    public function setPlats(?array $plats): self { $this->plats = $plats; return $this; }

    public function getGalerie(): ?array { return $this->galerie; }
    public function setGalerie(?array $galerie): self { $this->galerie = $galerie; return $this; }

    public function getTheme(): ?string { return $this->theme; }
    public function setTheme(?string $theme): self { $this->theme = $theme; return $this; }

    public function getThemeLibelle(): ?string { return $this->themeLibelle; }
    public function setThemeLibelle(?string $themeLibelle): self { $this->themeLibelle = $themeLibelle; return $this; }

    public function getRegime(): ?string { return $this->regime; }
    public function setRegime(?string $regime): self { $this->regime = $regime; return $this; }

    public function getAllergenes(): ?string { return $this->allergenes; }
    public function setAllergenes(?string $allergenes): self { $this->allergenes = $allergenes; return $this; }

    public function jsonSerialize(): mixed
    {
        return [
            'menu_id' => $this->menuId,
            'titre' => $this->titre,
            'description' => $this->description,
            'prix_par_personne' => $this->prixParPersonne,
            'nombre_personne_minimum' => $this->nombrePersonneMinimum,
            'quantite_restante' => $this->quantiteRestante,
            'conditions' => $this->conditions,
            'image_principale' => $this->imagePrincipale,
            'actif' => $this->actif,
            'theme' => $this->theme,
            'theme_libelle' => $this->themeLibelle,
            'regime' => $this->regime,
            'allergenes' => $this->allergenes,
            'photos' => $this->galerie,
        ];
    }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['menu_id'])) $entity->setMenuId((int) $data['menu_id']);
        if (isset($data['titre'])) $entity->setTitre($data['titre']);
        if (isset($data['nombre_personne_minimum'])) $entity->setNombrePersonneMinimum((int) $data['nombre_personne_minimum']);
        if (isset($data['prix_par_personne'])) $entity->setPrixParPersonne((float) $data['prix_par_personne']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description']);
        if (array_key_exists('conditions', $data)) $entity->setConditions($data['conditions']);
        if (isset($data['quantite_restante'])) $entity->setQuantiteRestante((int) $data['quantite_restante']);
        if (array_key_exists('image_principale', $data)) $entity->setImagePrincipale($data['image_principale']);
        if (isset($data['actif'])) $entity->setActif((bool) $data['actif']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        if (isset($data['theme'])) $entity->setTheme($data['theme']);
        if (isset($data['theme_libelle'])) $entity->setThemeLibelle($data['theme_libelle']);
        if (isset($data['regime'])) $entity->setRegime($data['regime']);
        if (isset($data['allergenes'])) $entity->setAllergenes($data['allergenes']);
        return $entity;
    }
}
