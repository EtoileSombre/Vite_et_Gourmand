<?php

namespace App\Models;

class User
{
    private ?int $utilisateurId = null;
    private string $email;
    private string $password;
    private ?string $prenom = null;
    private ?string $nom = null;
    private ?string $telephone = null;
    private ?string $ville = null;
    private string $pays = 'France';
    private ?string $adressePostale = null;
    private ?string $codePostal = null;
    private bool $actif = true;
    private int $roleId;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    // Champs joints
    private ?string $roleLibelle = null;

    public function getUtilisateurId(): ?int { return $this->utilisateurId; }
    public function setUtilisateurId(int $utilisateurId): self { $this->utilisateurId = $utilisateurId; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }

    public function getPays(): string { return $this->pays; }
    public function setPays(string $pays): self { $this->pays = $pays; return $this; }

    public function getAdressePostale(): ?string { return $this->adressePostale; }
    public function setAdressePostale(?string $adressePostale): self { $this->adressePostale = $adressePostale; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(?string $codePostal): self { $this->codePostal = $codePostal; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    public function getRoleId(): int { return $this->roleId; }
    public function setRoleId(int $roleId): self { $this->roleId = $roleId; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getRoleLibelle(): ?string { return $this->roleLibelle; }
    public function setRoleLibelle(?string $roleLibelle): self { $this->roleLibelle = $roleLibelle; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['utilisateur_id'])) $entity->setUtilisateurId((int) $data['utilisateur_id']);
        if (isset($data['email'])) $entity->setEmail($data['email']);
        if (isset($data['password'])) $entity->setPassword($data['password']);
        if (array_key_exists('prenom', $data)) $entity->setPrenom($data['prenom']);
        if (array_key_exists('nom', $data)) $entity->setNom($data['nom']);
        if (array_key_exists('telephone', $data)) $entity->setTelephone($data['telephone']);
        if (array_key_exists('ville', $data)) $entity->setVille($data['ville']);
        if (isset($data['pays'])) $entity->setPays($data['pays']);
        if (array_key_exists('adresse_postale', $data)) $entity->setAdressePostale($data['adresse_postale']);
        if (array_key_exists('code_postal', $data)) $entity->setCodePostal($data['code_postal']);
        if (isset($data['actif'])) $entity->setActif((bool) $data['actif']);
        if (isset($data['role_id'])) $entity->setRoleId((int) $data['role_id']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        if (isset($data['role_libelle'])) $entity->setRoleLibelle($data['role_libelle']);
        if (isset($data['role'])) $entity->setRoleLibelle($data['role']);
        if (isset($data['role_nom'])) $entity->setRoleLibelle($data['role_nom']);
        return $entity;
    }
}
