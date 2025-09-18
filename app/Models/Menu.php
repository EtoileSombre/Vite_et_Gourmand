<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Menu
 * Gère les interactions avec la table menus
 */
class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';

    /**
     * Récupère tous les menus actifs (avec stock disponible)
     * 
     * @return array
     */
    public function findActive(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE quantite_restante > 0 
            ORDER BY {$this->primaryKey} DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un menu actif par son ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findActiveById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE {$this->primaryKey} = :id 
            AND quantite_restante > 0
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupère les menus par thème
     * 
     * @param int $themeId
     * @return array
     */
    public function findByTheme(int $themeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE theme_id = :theme_id 
            AND quantite_restante > 0
        ");
        $stmt->execute(['theme_id' => $themeId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les menus (pour admin)
     * 
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC");
        return $stmt->fetchAll();
    }
}

