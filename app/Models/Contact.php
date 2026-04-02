<?php

namespace App\Models;

class Contact
{
    private ?int $contactId = null;
    private ?string $nom = null;
    private string $email;
    private string $sujet;
    private string $message;
    private string $statut = 'nouveau';
    private ?string $createdAt = null;

    public function getContactId(): ?int { return $this->contactId; }
    public function setContactId(int $contactId): self { $this->contactId = $contactId; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getSujet(): string { return $this->sujet; }
    public function setSujet(string $sujet): self { $this->sujet = $sujet; return $this; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['contact_id'])) $entity->setContactId((int) $data['contact_id']);
        if (array_key_exists('nom', $data)) $entity->setNom($data['nom']);
        if (isset($data['email'])) $entity->setEmail($data['email']);
        if (isset($data['sujet'])) $entity->setSujet($data['sujet']);
        if (isset($data['message'])) $entity->setMessage($data['message']);
        if (isset($data['statut'])) $entity->setStatut($data['statut']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        return $entity;
    }
}
