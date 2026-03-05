<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class PlatRepository implements PlatRepositoryInterface
{
    private PDO $db;
    private string $table = 'plat';
    private string $primaryKey = 'plat_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAllPlats(?string $typePlat = null): array
    {
        $sql = "SELECT plat_id, titre_plat, description, type_plat, photo, created_at, updated_at
                FROM {$this->table}";
        
        if ($typePlat) {
            $sql .= " WHERE type_plat = ?";
        }
        
        $sql .= " ORDER BY type_plat ASC, titre_plat ASC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($typePlat) {
            $stmt->execute([$typePlat]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findPlatById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    //Compte le nombre de plats par type
    public function countByType(): array
    {
        $stmt = $this->db->query("
            SELECT type_plat, COUNT(*) as total
            FROM {$this->table}
            GROUP BY type_plat
            ORDER BY type_plat ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTypesPlat(): array
    {
        return ['Entrée', 'Plat', 'Dessert', 'Accompagnement'];
    }
    public function createPlat(array $data): ?int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (titre_plat, description, type_plat, photo)
            VALUES (?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $data['titre_plat'],
            $data['description'] ?? null,
            $data['type_plat'] ?? 'Plat',
            $data['photo'] ?? null
        ]);
        
        return $success ? (int)$this->db->lastInsertId() : null;
    }
    public function updatePlat(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET titre_plat = ?,
                description = ?,
                type_plat = ?,
                photo = ?
            WHERE {$this->primaryKey} = ?
        ");
        
        return $stmt->execute([
            $data['titre_plat'],
            $data['description'] ?? null,
            $data['type_plat'] ?? 'Plat',
            $data['photo'] ?? null,
            $id
        ]);
    }
    public function deletePlat(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE {$this->primaryKey} = ?
        ");
        return $stmt->execute([$id]);
    }
    public function getAllAllergenes(): array
    {
        $stmt = $this->db->query("SELECT allergene_id, libelle FROM allergene ORDER BY libelle");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllergenesForPlat(int $platId): array
    {
        $stmt = $this->db->prepare("
            SELECT allergene_id 
            FROM contient 
            WHERE plat_id = ?
        ");
        $stmt->execute([$platId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'allergene_id');
    }

    //Synchronise les allergènes d'un plat (ajoute les nouveaux et supprime les anciens)
    public function syncAllergenes(int $platId, array $allergeneIds): bool
    {
        try {
            // Supprimer les anciennes associations
            $stmt = $this->db->prepare("DELETE FROM contient WHERE plat_id = ?");
            $stmt->execute([$platId]);
            
            // Ajouter les nouvelles associations
            if (!empty($allergeneIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO contient (plat_id, allergene_id)
                    VALUES (?, ?)
                ");
                foreach ($allergeneIds as $allergeneId) {
                    $stmt->execute([$platId, $allergeneId]);
                }
            }
            
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}