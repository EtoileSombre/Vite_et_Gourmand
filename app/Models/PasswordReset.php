<?php

namespace App\Models;

class PasswordReset
{
    private ?int $id = null;
    private string $email;
    private string $token;
    private string $expiresAt;
    private ?string $usedAt = null;
    private ?string $createdAt = null;

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getToken(): string { return $this->token; }
    public function setToken(string $token): self { $this->token = $token; return $this; }

    public function getExpiresAt(): string { return $this->expiresAt; }
    public function setExpiresAt(string $expiresAt): self { $this->expiresAt = $expiresAt; return $this; }

    public function getUsedAt(): ?string { return $this->usedAt; }
    public function setUsedAt(?string $usedAt): self { $this->usedAt = $usedAt; return $this; }

    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function setCreatedAt(?string $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function isExpired(): bool
    {
        return strtotime($this->expiresAt) < time();
    }

    public function isUsed(): bool
    {
        return $this->usedAt !== null;
    }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['id'])) $entity->setId((int) $data['id']);
        if (isset($data['email'])) $entity->setEmail($data['email']);
        if (isset($data['token'])) $entity->setToken($data['token']);
        if (isset($data['expires_at'])) $entity->setExpiresAt($data['expires_at']);
        if (array_key_exists('used_at', $data)) $entity->setUsedAt($data['used_at']);
        if (isset($data['created_at'])) $entity->setCreatedAt($data['created_at']);
        return $entity;
    }
}
