<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle PasswordReset
 * Gère les tokens de réinitialisation de mot de passe
 */
class PasswordReset extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';

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
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Marque un token comme utilisé
     * 
     * @return bool
     */
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
