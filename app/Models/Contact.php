<?php

namespace App\Models;

use App\Core\Model;

class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'contact_id';

    /**
     * Crée un nouveau message de contact
     * 
     * @return int
     */
    public function createContact(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (nom, email, sujet, message, statut, created_at)
            VALUES (:nom, :email, :sujet, :message, 'nouveau', NOW())
        ");
        
        $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'sujet' => $data['sujet'] ?? 'Demande de contact',
            'message' => $data['message']
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Récupère tous les messages de contact
     * 
     * @return array
     */
    public function findAllContacts(?string $statut = null): array
    {
        if ($statut) {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE statut = :statut
                ORDER BY created_at DESC
            ");
            $stmt->execute(['statut' => $statut]);
        } else {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Compte les messages par statut
     * 
     * @return int
     */
    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM {$this->table} 
            WHERE statut = :statut
        ");
        $stmt->execute(['statut' => $statut]);
        $result = $stmt->fetch();
        
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Met à jour le statut d'un message
     * 
     * @return bool
     */
    public function updateStatut(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET statut = :statut 
            WHERE {$this->primaryKey} = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'statut' => $statut
        ]);
    }
}
