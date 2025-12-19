<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model pour la table commande_menu
 * Gère les lignes de commande (plusieurs menus par commande)
 */
class CommandeMenu extends Model
{
    protected $table = 'commande_menu';
    protected $primaryKey = 'commande_menu_id';

    /**
     * Récupérer tous les menus d'une commande
     */
    public function findByCommande($numeroCommande)
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
        return $stmt->fetchAll();
    }

    /**
     * Ajouter une ligne de menu à une commande
     */
    public function addMenuToCommande($numeroCommande, $menuId, $nombrePersonne, $prixParPersonne, $reduction = 0)
    {
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

    /**
     * Mettre à jour une ligne de commande
     */
    public function updateLigne($commandeMenuId, $nombrePersonne, $prixParPersonne, $reduction = 0)
    {
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

    /**
     * Supprimer une ligne de commande
     */
    public function deleteLigne($commandeMenuId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE commande_menu_id = ?
        ");
        return $stmt->execute([$commandeMenuId]);
    }

    /**
     * Calculer le total des menus d'une commande
     */
    public function getTotalMenus($numeroCommande)
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_ligne), 0) as total
            FROM {$this->table}
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ? (float)$result['total'] : 0;
    }

    /**
     * Calculer le nombre total de personnes pour une commande
     */
    public function getTotalPersonnes($numeroCommande)
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(nombre_personne), 0) as total
            FROM {$this->table}
            WHERE numero_commande = ?
        ");
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Récupérer les détails d'une ligne spécifique
     */
    public function findById($commandeMenuId)
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
        return $stmt->fetch();
    }
}
