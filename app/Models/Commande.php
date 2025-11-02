<?php

namespace App\Models;

use App\Core\Model;

class Commande extends Model
{
    protected $table = 'commande';

    /**
     * Récupère toutes les commandes avec détails
     * 
     * @return array
     */
    public function findAll()
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.titre as menu_nom, m.prix_par_personne as menu_prix 
            FROM commande c
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Trouve toutes les commandes d'un utilisateur
     * 
     * @param int $userId
     * @return array
     */
    public function findByUser($userId)
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.titre as menu_nom, m.prix_par_personne as menu_prix 
            FROM commande c
            LEFT JOIN menu m ON c.menu_id = m.menu_id
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
    public function findWithDetails($id)
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.titre as menu_nom, m.prix_par_personne as menu_prix, u.nom as utilisateur_nom
            FROM commande c
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.commande_id = ?
        ');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouve une commande par son numéro
     * 
     * @param string $numeroCommande
     * @return array|null
     */
    public function findByNumero($numeroCommande)
    {
        $stmt = $this->db->prepare('
            SELECT c.*, m.titre as menu_nom, m.prix_par_personne as menu_prix 
            FROM commande c
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.numero_commande = ?
        ');
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Met à jour une commande par son numéro
     * 
     * @param string $numeroCommande
     * @param array $data
     * @return bool
     */
    public function updateByNumero($numeroCommande, $data)
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = :$field";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE numero_commande = :numero_commande";
        
        $data['numero_commande'] = $numeroCommande;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
}
