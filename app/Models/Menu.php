<?php

namespace App\Models;

use App\Core\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';

    public function findActive(): array
    {
        $stmt = $this->db->query("
            SELECT m.*, t.libelle as theme
            FROM {$this->table} m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            WHERE m.quantite_restante > 0 
            ORDER BY m.{$this->primaryKey} DESC
        ");
        return $stmt->fetchAll();
    }

    public function findActiveById(int $id): ?array
    {
        // Récupérer les informations du menu avec thème et régime
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   t.libelle as theme_libelle,
                   r.libelle as regime_libelle
            FROM {$this->table} m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            LEFT JOIN regime r ON m.regime_id = r.regime_id
            WHERE m.{$this->primaryKey} = :id 
            AND m.quantite_restante > 0
        ");
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        
        if (!$menu) {
            return null;
        }

        // Récupérer les plats du menu avec leurs allergènes
        $stmt = $this->db->prepare("
            SELECT p.plat_id, p.titre_plat,
                   GROUP_CONCAT(DISTINCT a.libelle SEPARATOR ', ') as allergenes
            FROM propose pr
            JOIN plat p ON pr.plat_id = p.plat_id
            LEFT JOIN consent c ON p.plat_id = c.plat_id
            LEFT JOIN allergene a ON c.allergene_id = a.allergene_id
            WHERE pr.menu_id = :menu_id
            GROUP BY p.plat_id, p.titre_plat
            ORDER BY p.plat_id
        ");
        $stmt->execute(['menu_id' => $id]);
        $menu['plats'] = $stmt->fetchAll();

        return $menu;
    }

    /**
     * Récupère les menus par thème
     * 
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

    /**
     * Récupère tous les thèmes disponibles
     * 
     * @return array
     */
    public function getAllThemes(): array
    {
        $stmt = $this->db->query("SELECT theme_id, libelle FROM theme ORDER BY libelle ASC");
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les régimes disponibles
     * 
     * @return array
     */
    public function getAllRegimes(): array
    {
        $stmt = $this->db->query("SELECT regime_id, libelle FROM regime ORDER BY libelle ASC");
        return $stmt->fetchAll();
    }
}

