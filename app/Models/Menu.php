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
        // Récupérer les informations du menu avec thème
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   t.libelle as theme_libelle
            FROM {$this->table} m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            WHERE m.{$this->primaryKey} = :id 
            AND m.quantite_restante > 0
        ");
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        
        if (!$menu) {
            return null;
        }

        // Récupérer les régimes adaptés
        $stmt = $this->db->prepare("
            SELECT GROUP_CONCAT(r.libelle SEPARATOR ', ') as regimes
            FROM adapte a
            JOIN regime r ON a.regime_id = r.regime_id
            WHERE a.menu_id = :menu_id
        ");
        $stmt->execute(['menu_id' => $id]);
        $regimes = $stmt->fetch();
        $menu['regime_libelle'] = $regimes['regimes'] ?? null;

        // Récupérer les plats du menu avec leurs allergènes
        $stmt = $this->db->prepare("
            SELECT p.plat_id, p.titre_plat, p.description, p.type_plat, pr.ordre,
                   GROUP_CONCAT(DISTINCT a.libelle SEPARATOR ', ') as allergenes
            FROM propose pr
            JOIN plat p ON pr.plat_id = p.plat_id
            LEFT JOIN contient c ON p.plat_id = c.plat_id
            LEFT JOIN allergene a ON c.allergene_id = a.allergene_id
            WHERE pr.menu_id = :menu_id
            GROUP BY p.plat_id, p.titre_plat, p.description, p.type_plat, pr.ordre
            ORDER BY pr.ordre, p.plat_id
        ");
        $stmt->execute(['menu_id' => $id]);
        $menu['plats'] = $stmt->fetchAll();

        return $menu;
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

    /**
     * Récupère toutes les boissons disponibles groupées par type
     * 
     * @return array
     */
    public function getAllBoissons(): array
    {
        $stmt = $this->db->query("
            SELECT boisson_id, nom, type_boisson, prix_unitaire, contenance, description
            FROM boisson
            WHERE disponible = 1
            ORDER BY type_boisson, nom
        ");
        $boissons = $stmt->fetchAll();
        
        // Grouper par type
        $grouped = [];
        foreach ($boissons as $boisson) {
            $type = $boisson['type_boisson'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $boisson;
        }
        
        return $grouped;
    }

    /**
     * Récupère tout le matériel disponible groupé par catégorie
     * 
     * @return array
     */
    public function getAllMateriel(): array
    {
        $stmt = $this->db->query("
            SELECT materiel_id, nom, categorie, prix_caution, quantite_disponible, description
            FROM materiel
            WHERE actif = 1 AND quantite_disponible > 0
            ORDER BY categorie, nom
        ");
        $materiels = $stmt->fetchAll();
        
        // Grouper par catégorie
        $grouped = [];
        foreach ($materiels as $materiel) {
            $categorie = $materiel['categorie'];
            if (!isset($grouped[$categorie])) {
                $grouped[$categorie] = [];
            }
            $grouped[$categorie][] = $materiel;
        }
        
        return $grouped;
    }
}

