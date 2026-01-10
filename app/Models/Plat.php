<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Plat extends Model
{
    protected $table = 'plat';

    /**
     * Récupère tous les plats avec filtre optionnel par type
     */
    public static function findAllPlats(?string $typePlat = null): array
    {
        $db = Database::getInstance();
        
        $sql = "SELECT plat_id, titre_plat, description, type_plat, photo, created_at, updated_at
                FROM plat";
        
        if ($typePlat) {
            $sql .= " WHERE type_plat = :type_plat";
        }
        
        $sql .= " ORDER BY type_plat ASC, titre_plat ASC";
        
        $stmt = $db->prepare($sql);
        
        if ($typePlat) {
            $stmt->execute(['type_plat' => $typePlat]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un plat par son ID
     */
    public static function findPlatById(int $platId): ?array
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT plat_id, titre_plat, description, type_plat, photo, created_at, updated_at
            FROM plat
            WHERE plat_id = :plat_id
        ");
        
        $stmt->execute(['plat_id' => $platId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Crée un nouveau plat
     */
    public static function createPlat(array $data): ?int
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO plat (titre_plat, description, type_plat, photo)
            VALUES (:titre_plat, :description, :type_plat, :photo)
        ");
        
        $success = $stmt->execute([
            'titre_plat' => $data['titre_plat'],
            'description' => $data['description'] ?? null,
            'type_plat' => $data['type_plat'] ?? 'Plat',
            'photo' => $data['photo'] ?? null
        ]);
        
        return $success ? (int)$db->lastInsertId() : null;
    }

    /**
     * Met à jour un plat
     */
    public static function updatePlat(int $platId, array $data): bool
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            UPDATE plat
            SET titre_plat = :titre_plat,
                description = :description,
                type_plat = :type_plat,
                photo = :photo
            WHERE plat_id = :plat_id
        ");
        
        return $stmt->execute([
            'plat_id' => $platId,
            'titre_plat' => $data['titre_plat'],
            'description' => $data['description'] ?? null,
            'type_plat' => $data['type_plat'] ?? 'Plat',
            'photo' => $data['photo'] ?? null
        ]);
    }

    /**
     * Supprime un plat
     */
    public static function deletePlat(int $platId): bool
    {
        $db = Database::getInstance();
        
        // La suppression CASCADE s'occupera de la table 'propose' et 'contient'
        $stmt = $db->prepare("DELETE FROM plat WHERE plat_id = :plat_id");
        
        return $stmt->execute(['plat_id' => $platId]);
    }

    /**
     * Compte le nombre de plats par type
     */
    public static function countByType(): array
    {
        $db = Database::getInstance();
        
        $stmt = $db->query("
            SELECT type_plat, COUNT(*) as total
            FROM plat
            GROUP BY type_plat
            ORDER BY type_plat ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les plats associés à un menu
     */
    public static function findPlatsByMenuId(int $menuId): array
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT p.plat_id, p.titre_plat, p.description, p.type_plat, p.photo, pr.ordre
            FROM plat p
            INNER JOIN propose pr ON p.plat_id = pr.plat_id
            WHERE pr.menu_id = :menu_id
            ORDER BY pr.ordre ASC, p.type_plat ASC
        ");
        
        $stmt->execute(['menu_id' => $menuId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Associe un plat à un menu
     */
    public static function attachToMenu(int $platId, int $menuId, int $ordre = 0): bool
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO propose (menu_id, plat_id, ordre)
            VALUES (:menu_id, :plat_id, :ordre)
            ON DUPLICATE KEY UPDATE ordre = :ordre
        ");
        
        return $stmt->execute([
            'menu_id' => $menuId,
            'plat_id' => $platId,
            'ordre' => $ordre
        ]);
    }

    /**
     * Dissocie un plat d'un menu
     */
    public static function detachFromMenu(int $platId, int $menuId): bool
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            DELETE FROM propose
            WHERE menu_id = :menu_id AND plat_id = :plat_id
        ");
        
        return $stmt->execute([
            'menu_id' => $menuId,
            'plat_id' => $platId
        ]);
    }

    /**
     * Types de plats disponibles
     */
    public static function getTypesPlat(): array
    {
        return ['Entrée', 'Plat', 'Dessert', 'Accompagnement'];
    }

    /**
     * Récupère tous les allergènes disponibles
     */
    public static function getAllAllergenes(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT allergene_id, libelle FROM allergene ORDER BY libelle");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les allergènes d'un plat
     */
    public static function getAllergenesForPlat(int $platId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT allergene_id 
            FROM contient 
            WHERE plat_id = :plat_id
        ");
        $stmt->execute(['plat_id' => $platId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'allergene_id');
    }

    /**
     * Associe des allergènes à un plat
     */
    public static function syncAllergenes(int $platId, array $allergeneIds): bool
    {
        $db = Database::getInstance();
        
        // Supprimer les anciennes associations
        $stmt = $db->prepare("DELETE FROM contient WHERE plat_id = :plat_id");
        $stmt->execute(['plat_id' => $platId]);
        
        // Ajouter les nouvelles associations
        if (!empty($allergeneIds)) {
            $stmt = $db->prepare("
                INSERT INTO contient (plat_id, allergene_id)
                VALUES (:plat_id, :allergene_id)
            ");
            foreach ($allergeneIds as $allergeneId) {
                $stmt->execute([
                    'plat_id' => $platId,
                    'allergene_id' => $allergeneId
                ]);
            }
        }
        
        return true;
    }
}
