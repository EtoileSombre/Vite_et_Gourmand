<?php

namespace App\Models;

use App\Core\Model;

class Commande extends Model
{
    protected $table = 'commandes';

    /**
     * Trouve toutes les commandes d'un utilisateur
     * 
     * @param int $userId
     * @return array
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.nom as menu_nom, m.prix as menu_prix 
            FROM commandes c
            LEFT JOIN menus m ON c.menu_id = m.id
            WHERE c.utilisateur_id = ?
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Trouve une commande avec son détail
     * 
     * @param int $id
     * @return array|null
     */
    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.nom as menu_nom, m.prix as menu_prix, u.nom as utilisateur_nom
            FROM commandes c
            LEFT JOIN menus m ON c.menu_id = m.id
            LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
            WHERE c.id = ?
        ');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
