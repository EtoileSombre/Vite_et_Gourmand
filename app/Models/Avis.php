<?php

namespace App\Models;

class Avis
{
    private ?int $avisId = null;
    private int $note;
    private ?string $description = null;
    private string $statut = 'en_attente';
    private int $utilisateurId;
    private ?string $numeroCommande = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Champs joints (depuis utilisateur)
    private ?string $prenom = null;
    private ?string $nom = null;
    private ?string $email = null;

    // Champs joints (depuis menu via commande)
    private ?string $menuTitre = null;

    public function getAvisId(): ?int { return $this->avisId; }
    public function setAvisId(int $avisId): self { $this->avisId = $avisId; return $this; }

    public function getNote(): int { return $this->note; }
    public function setNote(int $note): self { $this->note = $note; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getUtilisateurId(): int { return $this->utilisateurId; }
    public function setUtilisateurId(int $utilisateurId): self { $this->utilisateurId = $utilisateurId; return $this; }

    public function getNumeroCommande(): ?string { return $this->numeroCommande; }
    public function setNumeroCommande(?string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getMenuTitre(): ?string { return $this->menuTitre; }
    public function setMenuTitre(?string $menuTitre): self { $this->menuTitre = $menuTitre; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['avis_id'])) $entity->setAvisId((int) $data['avis_id']);
        if (isset($data['note'])) $entity->setNote((int) $data['note']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description']);
        if (isset($data['statut'])) $entity->setStatut($data['statut']);
        if (isset($data['utilisateur_id'])) $entity->setUtilisateurId((int) $data['utilisateur_id']);
        if (array_key_exists('numero_commande', $data)) $entity->setNumeroCommande($data['numero_commande']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        if (isset($data['prenom'])) $entity->setPrenom($data['prenom']);
        if (isset($data['utilisateur_prenom'])) $entity->setPrenom($data['utilisateur_prenom']);
        if (isset($data['nom'])) $entity->setNom($data['nom']);
        if (isset($data['utilisateur_nom'])) $entity->setNom($data['utilisateur_nom']);
        if (isset($data['email'])) $entity->setEmail($data['email']);
        if (isset($data['utilisateur_email'])) $entity->setEmail($data['utilisateur_email']);
        if (isset($data['menu_titre'])) $entity->setMenuTitre($data['menu_titre']);
        return $entity;
    }
}
