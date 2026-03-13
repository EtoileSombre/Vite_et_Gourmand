<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class CommandeMenuRepository implements CommandeMenuRepositoryInterface
{
    private PDO $db;
    private string $table = 'commande_menu';
    private string $primaryKey = 'commande_menu_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findByCommande(string $numeroCommande): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                cm.*,
                m.titre as menu_nom,
                m.description as menu_description,
                m.image_principale
            FROM {$this->table} cm
            JOIN menu m ON cm.menu_id = m.menu_id
            WHERE cm.numero_commande = ?
            ORDER BY cm.commande_menu_id ASC
        ");
        $stmt->execute([$numeroCommande]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Ajoute une ligne de menu à une commande
    public function addMenuToCommande(
        string $numeroCommande,
        int $menuId,
        int $nombrePersonne,
        float $prixParPersonne,
        float $reduction = 0
    ): bool {
        $totalLigne = ($nombrePersonne * $prixParPersonne) - $reduction;
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (numero_commande, menu_id, quantite, nombre_personne, prix_par_personne, reduction, total_ligne)
            VALUES (?, ?, 1, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $numeroCommande,
            $menuId,
            $nombrePersonne,
            $prixParPersonne,
            $reduction,
            $totalLigne
        ]);
    }
    public function updateLigne(
        int $commandeMenuId,
        int $nombrePersonne,
        float $prixParPersonne,
        float $reduction = 0
    ): bool {
        $totalLigne = ($nombrePersonne * $prixParPersonne) - $reduction;
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET nombre_personne = ?,
                prix_par_personne = ?,
                reduction = ?,
                total_ligne = ?
            WHERE commande_menu_id = ?
        ");
        
        return $stmt->execute([
            $nombrePersonne,
            $prixParPersonne,
            $reduction,
            $totalLigne,
            $commandeMenuId
        ]);
    }
    public function deleteLigne(int $commandeMenuId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE commande_menu_id = ?
        ");
        return $stmt->execute([$commandeMenuId]);
    }

    //Calcule le total des menus pour une commande
    public function getTotalMenus(string $numeroCommande): float
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_ligne), 0) as total
            FROM {$this->table}
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['total'] : 0;
    }

    //Calcule le nombre total de personnes pour une commande
    public function getTotalPersonnes(string $numeroCommande): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(nombre_personne), 0) as total
            FROM {$this->table}
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }
    public function findById(int $commandeMenuId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                cm.*,
                m.titre as menu_nom,
                m.description as menu_description
            FROM {$this->table} cm
            JOIN menu m ON cm.menu_id = m.menu_id
            WHERE cm.commande_menu_id = ?
        ");
        $stmt->execute([$commandeMenuId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    public function updateQuantite(string $numeroCommande, int $menuId, int $nombrePersonne): bool
    {
        // Récupérer le prix et la réduction actuels
        $stmt = $this->db->prepare("
            SELECT prix_par_personne, reduction
            FROM {$this->table}
            WHERE numero_commande = ? AND menu_id = ?
        ");
        $stmt->execute([$numeroCommande, $menuId]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ligne) {
            return false;
        }

        // Recalculer le total
        $prixParPersonne = $ligne['prix_par_personne'];
        $reduction = $ligne['reduction'];
        $totalLigne = ($nombrePersonne * $prixParPersonne) - $reduction;
        $updateStmt = $this->db->prepare("
            UPDATE {$this->table}
            SET nombre_personne = ?,
                total_ligne = ?
            WHERE numero_commande = ? AND menu_id = ?
        ");

        return $updateStmt->execute([
            $nombrePersonne,
            $totalLigne,
            $numeroCommande,
            $menuId
        ]);
    }
}