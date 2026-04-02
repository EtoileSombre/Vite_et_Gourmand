<?php

namespace App\Models;

class SuiviCommande
{
    private ?int $suiviId = null;
    private string $numeroCommande;
    private ?string $ancienStatut = null;
    private string $nouveauStatut;
    private ?string $commentaire = null;
    private ?int $employeId = null;
    private ?string $dateChangement = null;

    // Champs joints
    private ?string $employePrenom = null;
    private ?string $employeNom = null;

    public function getSuiviId(): ?int { return $this->suiviId; }
    public function setSuiviId(int $suiviId): self { $this->suiviId = $suiviId; return $this; }

    public function getNumeroCommande(): string { return $this->numeroCommande; }
    public function setNumeroCommande(string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }

    public function getAncienStatut(): ?string { return $this->ancienStatut; }
    public function setAncienStatut(?string $ancienStatut): self { $this->ancienStatut = $ancienStatut; return $this; }

    public function getNouveauStatut(): string { return $this->nouveauStatut; }
    public function setNouveauStatut(string $nouveauStatut): self { $this->nouveauStatut = $nouveauStatut; return $this; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): self { $this->commentaire = $commentaire; return $this; }

    public function getEmployeId(): ?int { return $this->employeId; }
    public function setEmployeId(?int $employeId): self { $this->employeId = $employeId; return $this; }

    public function getDateChangement(): ?string { return $this->dateChangement; }
    public function setDateChangement(?string $dateChangement): self { $this->dateChangement = $dateChangement; return $this; }

    public function getEmployePrenom(): ?string { return $this->employePrenom; }
    public function setEmployePrenom(?string $employePrenom): self { $this->employePrenom = $employePrenom; return $this; }

    public function getEmployeNom(): ?string { return $this->employeNom; }
    public function setEmployeNom(?string $employeNom): self { $this->employeNom = $employeNom; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['suivi_id'])) $entity->setSuiviId((int) $data['suivi_id']);
        if (isset($data['numero_commande'])) $entity->setNumeroCommande($data['numero_commande']);
        if (array_key_exists('ancien_statut', $data)) $entity->setAncienStatut($data['ancien_statut']);
        if (isset($data['nouveau_statut'])) $entity->setNouveauStatut($data['nouveau_statut']);
        if (array_key_exists('commentaire', $data)) $entity->setCommentaire($data['commentaire']);
        if (array_key_exists('employe_id', $data)) $entity->setEmployeId($data['employe_id'] !== null ? (int) $data['employe_id'] : null);
        if (isset($data['date_changement'])) $entity->setDateChangement($data['date_changement']);
        if (isset($data['employe_prenom'])) $entity->setEmployePrenom($data['employe_prenom']);
        if (isset($data['employe_nom'])) $entity->setEmployeNom($data['employe_nom']);
        return $entity;
    }
}
