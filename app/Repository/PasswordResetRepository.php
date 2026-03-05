<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    private PDO $db;
    private string $table = 'password_resets';
    private string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function createToken(string $email, string $token, string $expiresAt): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (email, token, expires_at)
            VALUES (:email, :token, :expires_at)
        ");
        
        return $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }
    public function findValidToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE token = :token 
            AND expires_at > NOW()
            AND used_at IS NULL
        ");
        
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
    public function markTokenAsUsed(string $token): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET used_at = NOW()
            WHERE token = :token
        ");
        
        return $stmt->execute(['token' => $token]);
    }
}