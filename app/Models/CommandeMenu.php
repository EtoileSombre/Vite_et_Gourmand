<?php

namespace App\Models;

class CommandeMenu
{
    private ?int $commandeMenuId = null;
    private string $numeroCommande;
    private int $menuId;
    private int $quantite = 1;
    private int $nombrePersonne;
    private float $prixParPersonne;
    private float $reduction = 0.00;
    private float $totalLigne;

    // Champs joints
    private ?string $menuNom = null;
    private ?string $menuTitre = null;

    public function getCommandeMenuId(): ?int { return $this->commandeMenuId; }
    public function setCommandeMenuId(int $commandeMenuId): self { $this->commandeMenuId = $commandeMenuId; return $this; }

    public function getNumeroCommande(): string { return $this->numeroCommande; }
    public function setNumeroCommande(string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }

    public function getMenuId(): int { return $this->menuId; }
    public function setMenuId(int $menuId): self { $this->menuId = $menuId; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = $quantite; return $this; }

    public function getNombrePersonne(): int { return $this->nombrePersonne; }
    public function setNombrePersonne(int $nombrePersonne): self { $this->nombrePersonne = $nombrePersonne; return $this; }

    public function getPrixParPersonne(): float { return $this->prixParPersonne; }
    public function setPrixParPersonne(float $prixParPersonne): self { $this->prixParPersonne = $prixParPersonne; return $this; }

    public function getReduction(): float { return $this->reduction; }
    public function setReduction(float $reduction): self { $this->reduction = $reduction; return $this; }

    public function getTotalLigne(): float { return $this->totalLigne; }
    public function setTotalLigne(float $totalLigne): self { $this->totalLigne = $totalLigne; return $this; }

    public function getMenuNom(): ?string { return $this->menuNom; }
    public function setMenuNom(?string $menuNom): self { $this->menuNom = $menuNom; return $this; }

    public function getMenuTitre(): ?string { return $this->menuTitre; }
    public function setMenuTitre(?string $menuTitre): self { $this->menuTitre = $menuTitre; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['commande_menu_id'])) $entity->setCommandeMenuId((int) $data['commande_menu_id']);
        if (isset($data['numero_commande'])) $entity->setNumeroCommande($data['numero_commande']);
        if (isset($data['menu_id'])) $entity->setMenuId((int) $data['menu_id']);
        if (isset($data['quantite'])) $entity->setQuantite((int) $data['quantite']);
        if (isset($data['nombre_personne'])) $entity->setNombrePersonne((int) $data['nombre_personne']);
        if (isset($data['prix_par_personne'])) $entity->setPrixParPersonne((float) $data['prix_par_personne']);
        if (isset($data['reduction'])) $entity->setReduction((float) $data['reduction']);
        if (isset($data['total_ligne'])) $entity->setTotalLigne((float) $data['total_ligne']);
        if (isset($data['menu_nom'])) $entity->setMenuNom($data['menu_nom']);
        if (isset($data['menu_titre'])) $entity->setMenuTitre($data['menu_titre']);
        if (isset($data['titre'])) $entity->setMenuTitre($data['titre']);
        return $entity;
    }
}
