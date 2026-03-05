<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class ContactRepository implements ContactRepositoryInterface
{
    private PDO $db;
    private string $table = 'contact';
    private string $primaryKey = 'contact_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

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
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM {$this->table} 
            WHERE statut = :statut
        ");
        $stmt->execute(['statut' => $statut]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) ($result['total'] ?? 0);
    }

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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE {$this->primaryKey} = :id
        ");
        
        return $stmt->execute(['id' => $id]);
    }
}