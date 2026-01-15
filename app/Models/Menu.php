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
            SELECT m.*,
                   GROUP_CONCAT(DISTINCT t.libelle SEPARATOR ', ') as theme,
                   GROUP_CONCAT(DISTINCT r.libelle SEPARATOR ', ') as regime
            FROM {$this->table} m
            LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
            LEFT JOIN theme t ON mt.theme_id = t.theme_id
            LEFT JOIN adapte a ON m.menu_id = a.menu_id
            LEFT JOIN regime r ON a.regime_id = r.regime_id
            WHERE m.quantite_restante > 0
            GROUP BY m.menu_id
            ORDER BY m.{$this->primaryKey} DESC
        ");
        return $stmt->fetchAll();
    }

    // Menus actifs avec photos (1 seule requête pour toutes les photos)
    public function findActiveWithPhotos(): array
    {
        $menus = $this->findActive();
        
        if (empty($menus)) {
            return [];
        }
        
        // Charger toutes les photos en une fois
        $menuIds = array_column($menus, 'menu_id');
        $placeholders = str_repeat('?,', count($menuIds) - 1) . '?';
        
        $stmt = $this->db->prepare("
            SELECT menu_id, image_url, legende, ordre
            FROM galerie_menu
            WHERE menu_id IN ($placeholders)
            ORDER BY menu_id, ordre ASC, galerie_id ASC
        ");
        $stmt->execute($menuIds);
        $photos = $stmt->fetchAll();
        
        // Grouper les photos par menu_id
        $photosParMenu = [];
        foreach ($photos as $photo) {
            $photosParMenu[$photo['menu_id']][] = $photo;
        }
        
        // Associer les photos aux menus
        foreach ($menus as &$menu) {
            $menu['photos'] = $photosParMenu[$menu['menu_id']] ?? [];
        }
        
        return $menus;
    }

    public function findActiveById(int $id): ?array
    {
        // Récupérer les informations du menu avec thèmes et régimes
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   GROUP_CONCAT(DISTINCT t.libelle SEPARATOR ', ') as theme_libelle,
                   GROUP_CONCAT(DISTINCT r.libelle SEPARATOR ', ') as regime
            FROM {$this->table} m
            LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
            LEFT JOIN theme t ON mt.theme_id = t.theme_id
            LEFT JOIN adapte a ON m.menu_id = a.menu_id
            LEFT JOIN regime r ON a.regime_id = r.regime_id
            WHERE m.{$this->primaryKey} = :id 
            AND m.quantite_restante > 0
            GROUP BY m.menu_id
        ");
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        
        if (!$menu) {
            return null;
        }

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

    // Photos d'un menu
    public function getPhotosMenu(int $menuId): array
    {
        $stmt = $this->db->prepare("
            SELECT image_url, legende, ordre
            FROM galerie_menu
            WHERE menu_id = :menu_id
            ORDER BY ordre ASC, galerie_id ASC
        ");
        $stmt->execute(['menu_id' => $menuId]);
        return $stmt->fetchAll();
    }

    // Import des photos depuis le dossier du menu
    public function importPhotosFromDirectory(int $menuId, string $menuTitre, string $imgBaseDir): int
    {
        $menuDir = $imgBaseDir . $menuTitre;
        
        if (!is_dir($menuDir)) {
            return 0;
        }
        
        // Exclure les photos de boissons
        $boissonsExclues = ['bordeaux', 'rouge', 'blanc', 'vin', 'café', 'cafe', 'eau', 'minérale', 'minerale', 'sauvignon'];
        
        $files = scandir($menuDir);
        $photosMenu = [];
        $ordre = 1;
        
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $fileName = strtolower(pathinfo($file, PATHINFO_FILENAME));
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Vérifier si c'est une photo de boisson
                $estBoisson = false;
                foreach ($boissonsExclues as $motExclu) {
                    if (strpos($fileName, $motExclu) !== false) {
                        $estBoisson = true;
                        break;
                    }
                }
                
                if (!$estBoisson) {
                    $photosMenu[] = [
                        'url' => '/assets/img/' . $menuTitre . '/' . $file,
                        'ordre' => $ordre++
                    ];
                }
            }
        }
        
        if (empty($photosMenu)) {
            return 0;
        }
        
        // Supprimer les anciennes photos
        $stmt = $this->db->prepare("DELETE FROM galerie_menu WHERE menu_id = :menu_id");
        $stmt->execute(['menu_id' => $menuId]);
        
        // Insérer les nouvelles photos
        $stmt = $this->db->prepare("
            INSERT INTO galerie_menu (menu_id, image_url, legende, ordre) 
            VALUES (:menu_id, :image_url, :legende, :ordre)
        ");
        
        foreach ($photosMenu as $photo) {
            $stmt->execute([
                'menu_id' => $menuId,
                'image_url' => $photo['url'],
                'legende' => null,
                'ordre' => $photo['ordre']
            ]);
        }
        
        return count($photosMenu);
    }

    // Menus d'un thème
    public function findByTheme(int $themeId): array
    {
        $stmt = $this->db->prepare("
            SELECT m.* 
            FROM {$this->table} m
            JOIN menu_theme mt ON m.menu_id = mt.menu_id
            WHERE mt.theme_id = :theme_id 
            AND m.quantite_restante > 0
        ");
        $stmt->execute(['theme_id' => $themeId]);
        return $stmt->fetchAll();
    }

    // Tous les menus (admin)
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC");
        return $stmt->fetchAll();
    }

    // Liste des thèmes
    public function getAllThemes(): array
    {
        $stmt = $this->db->query("SELECT theme_id, libelle FROM theme ORDER BY libelle ASC");
        return $stmt->fetchAll();
    }

    // Liste des régimes
    public function getAllRegimes(): array
    {
        $stmt = $this->db->query("SELECT regime_id, libelle FROM regime ORDER BY libelle ASC");
        return $stmt->fetchAll();
    }

    //Filtre les menus selon les critères spécifiés//
    public function findFiltered(array $filters): array
    {
        $sql = "
                SELECT DISTINCT m.*,
                       GROUP_CONCAT(DISTINCT t.libelle SEPARATOR ', ') as theme,
                       GROUP_CONCAT(DISTINCT r.libelle SEPARATOR ', ') as regime
                FROM {$this->table} m
                LEFT JOIN menu_theme mt ON m.menu_id = mt.menu_id
                LEFT JOIN theme t ON mt.theme_id = t.theme_id
                LEFT JOIN adapte a ON m.menu_id = a.menu_id
                LEFT JOIN regime r ON a.regime_id = r.regime_id
                WHERE m.quantite_restante > 0
            ";

            $params = [];

            // Filtre régime
            if (!empty($filters['regime'])) {
                $sql .= " AND EXISTS (
                    SELECT 1 FROM adapte a2
                    JOIN regime r2 ON a2.regime_id = r2.regime_id
                    WHERE a2.menu_id = m.menu_id
                    AND LOWER(r2.libelle) LIKE LOWER(:regime)
                )";
                $params['regime'] = '%' . $filters['regime'] . '%';
            }

            // Filtre thème
            if (!empty($filters['theme'])) {
                $sql .= " AND EXISTS (
                    SELECT 1 FROM menu_theme mt2
                    JOIN theme t2 ON mt2.theme_id = t2.theme_id
                    WHERE mt2.menu_id = m.menu_id
                    AND LOWER(t2.libelle) LIKE LOWER(:theme)
                )";
                $params['theme'] = '%' . $filters['theme'] . '%';
            }

            // Filtre nombre de personnes minimum
            if (isset($filters['minPersonnes']) && is_numeric($filters['minPersonnes']) && $filters['minPersonnes'] > 0) {
                $sql .= " AND m.nombre_personne_minimum <= :minPersonnes";
                $params['minPersonnes'] = (int)$filters['minPersonnes'];
            }

            // Filtre prix minimum
            if (isset($filters['prixMin']) && is_numeric($filters['prixMin']) && $filters['prixMin'] > 0) {
                $sql .= " AND m.prix_par_personne >= :prixMin";
                $params['prixMin'] = (float)$filters['prixMin'];
            }

            // Filtre prix maximum
            if (isset($filters['prixMax']) && is_numeric($filters['prixMax']) && $filters['prixMax'] > 0) {
                $sql .= " AND m.prix_par_personne <= :prixMax";
                $params['prixMax'] = (float)$filters['prixMax'];
            }

            $sql .= " GROUP BY m.menu_id ORDER BY m.{$this->primaryKey} DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $menus = $stmt->fetchAll();

            if (empty($menus)) {
                return [];
            }

            // Charger les photos pour tous les menus filtrés
            $menuIds = array_column($menus, 'menu_id');
            $placeholders = str_repeat('?,', count($menuIds) - 1) . '?';
            
            $stmt = $this->db->prepare("
                SELECT menu_id, image_url, legende, ordre
                FROM galerie_menu
                WHERE menu_id IN ($placeholders)
                ORDER BY menu_id, ordre ASC, galerie_id ASC
            ");
            $stmt->execute($menuIds);
            $photos = $stmt->fetchAll();
            
            // Grouper les photos par menu_id
            $photosParMenu = [];
            foreach ($photos as $photo) {
                $photosParMenu[$photo['menu_id']][] = $photo;
            }
            
            // Associer les photos aux menus
            foreach ($menus as &$menu) {
                $menu['photos'] = $photosParMenu[$menu['menu_id']] ?? [];
            }

            return $menus;
    }

    /**
     * Récupère les plats associés à un menu
     * 
     * @return array
     */
    public function getPlatsForMenu(int $menuId): array
    {
        $stmt = $this->db->prepare("
            SELECT p.titre_plat, p.type_plat
            FROM propose pr
            JOIN plat p ON pr.plat_id = p.plat_id
            WHERE pr.menu_id = :menu_id
            ORDER BY pr.ordre, p.type_plat
        ");
        $stmt->execute(['menu_id' => $menuId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les IDs des plats associés à un menu
     * 
     * @return array
     */
    public function getPlatIdsForMenu(int $menuId): array
    {
        $stmt = $this->db->prepare("
            SELECT plat_id, ordre
            FROM propose
            WHERE menu_id = :menu_id
            ORDER BY ordre
        ");
        $stmt->execute(['menu_id' => $menuId]);
        $platsMenu = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($platsMenu, 'plat_id');
    }

    /**
     * Associe des plats à un menu
     * 
     * @return bool
     */
    public function syncPlats(int $menuId, array $platIds): bool
    {
        try {
            // Supprimer les anciennes associations
            $stmt = $this->db->prepare("DELETE FROM propose WHERE menu_id = :menu_id");
            $stmt->execute(['menu_id' => $menuId]);

            // Ajouter les nouvelles associations
            if (!empty($platIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO propose (menu_id, plat_id, ordre)
                    VALUES (:menu_id, :plat_id, :ordre)
                ");
                foreach ($platIds as $ordre => $platId) {
                    $stmt->execute([
                        'menu_id' => $menuId,
                        'plat_id' => $platId,
                        'ordre' => $ordre
                    ]);
                }
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}

