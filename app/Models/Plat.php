<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Plat
 * Gestion des plats et de leurs allergènes
 */
class Plat extends Model
{
    protected $table = 'plat';
    protected $primaryKey = 'plat_id';

    /**
     * Récupère tous les plats avec filtre optionnel par type (surcharge de findAll)
     * 
     * @return array
     */
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
        
        return $stmt->fetchAll();
    }

    /**
     * Récupère un plat par son ID (wrapper sur findById du parent)
     * 
     * @return array|null
     */
    public function findPlatById(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * Crée un nouveau plat
     * 
     * @return int|null ID du plat créé
     */
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

    /**
     * Met à jour un plat
     * 
     * @return bool
     */
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

    /**
     * Supprime un plat (wrapper sur delete du parent)
     * 
     * @return bool
     */
    public function deletePlat(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Compte le nombre de plats par type
     * 
     * @return array
     */
    public function countByType(): array
    {
        $stmt = $this->db->query("
            SELECT type_plat, COUNT(*) as total
            FROM {$this->table}
            GROUP BY type_plat
            ORDER BY type_plat ASC
        ");
        
        return $stmt->fetchAll();
    }

    /**
     * Récupère les plats associés à un menu
     * 
     * @return array
     */
    public function findPlatsByMenuId(int $menuId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.plat_id, p.titre_plat, p.description, p.type_plat, p.photo, pr.ordre
            FROM {$this->table} p
            INNER JOIN propose pr ON p.plat_id = pr.plat_id
            WHERE pr.menu_id = ?
            ORDER BY pr.ordre ASC, p.type_plat ASC
        ");
        
        $stmt->execute([$menuId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les plats d un type spécifique
     * 
     * @return array
     */
    public function findPlatsByType(string $type): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
            WHERE type_plat = ?
            ORDER BY titre_plat ASC
        ");
        
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    /**
     * Associe un plat à un menu
     * 
     * @return bool
     */
    public function attachToMenu(int $platId, int $menuId, int $ordre = 0): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO propose (menu_id, plat_id, ordre)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE ordre = ?
        ");
        
        return $stmt->execute([$menuId, $platId, $ordre, $ordre]);
    }

    /**
     * Dissocie un plat d un menu
     * 
     * @return bool
     */
    public function detachFromMenu(int $platId, int $menuId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM propose
            WHERE menu_id = ? AND plat_id = ?
        ");
        
        return $stmt->execute([$menuId, $platId]);
    }

    /**
     * Associe plusieurs plats à un menu
     * 
     * @return bool
     */
    public function syncPlatsWithMenu(int $menuId, array $platIds): bool
    {
        try {
            // Supprimer les anciennes associations
            $stmt = $this->db->prepare("DELETE FROM propose WHERE menu_id = ?");
            $stmt->execute([$menuId]);

            // Ajouter les nouvelles associations
            if (!empty($platIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO propose (menu_id, plat_id, ordre)
                    VALUES (?, ?, ?)
                ");
                foreach ($platIds as $ordre => $platId) {
                    $stmt->execute([$menuId, $platId, $ordre]);
                }
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Types de plats disponibles
     * 
     * @return array
     */
    public function getTypesPlat(): array
    {
        return ['Entrée', 'Plat', 'Dessert', 'Accompagnement'];
    }

    /**
     * Récupère tous les allergènes disponibles
     * 
     * @return array
     */
    public function getAllAllergenes(): array
    {
        $stmt = $this->db->query("SELECT allergene_id, libelle FROM allergene ORDER BY libelle");
        return $stmt->fetchAll();
    }

    /**
     * Récupère les allergènes d un plat
     * 
     * @return array
     */
    public function getAllergenesForPlat(int $platId): array
    {
        $stmt = $this->db->prepare("
            SELECT allergene_id 
            FROM contient 
            WHERE plat_id = ?
        ");
        $stmt->execute([$platId]);
        return array_column($stmt->fetchAll(), 'allergene_id');
    }

    /**
     * Récupère les allergènes détaillés d un plat
     * 
     * @return array
     */
    public function getAllergenesDetailsForPlat(int $platId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.allergene_id, a.libelle
            FROM allergene a
            INNER JOIN contient c ON a.allergene_id = c.allergene_id
            WHERE c.plat_id = ?
            ORDER BY a.libelle
        ");
        $stmt->execute([$platId]);
        return $stmt->fetchAll();
    }

    /**
     * Associe des allergènes à un plat
     * 
     * @return bool
     */
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

    /**
     * Vérifie si un plat contient un allergène spécifique
     * 
     * @return bool
     */
    public function hasAllergene(int $platId, int $allergeneId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM contient
            WHERE plat_id = ? AND allergene_id = ?
        ");
        $stmt->execute([$platId, $allergeneId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
