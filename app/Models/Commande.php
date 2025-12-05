<?php

namespace App\Models;

use App\Core\Model;

class Commande extends Model
{
    protected $table = 'commande';

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

    public function findByNumero($numeroCommande)
    {
        $stmt = $this->db->prepare('
            SELECT c.*, 
                   u.prenom as client_prenom, 
                   u.email as client_email,
                   u.telephone as client_telephone,
                   m.titre as menu_titre,
                   m.description as menu_description,
                   m.prix_par_personne as menu_prix,
                   m.regime as menu_regime
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.numero_commande = ?
        ');
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouve toutes les commandes avec détails (utilisateur + menu)
     * 
     * @return array
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as client_prenom, 
                   u.email as client_email,
                   u.telephone as client_telephone,
                   m.titre as menu_titre,
                   m.description as menu_description,
                   m.prix_par_personne as menu_prix,
                   m.regime as menu_regime
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            ORDER BY c.date_prestation DESC, c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Met à jour une commande par son numéro
     * 
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

    /**
     * Trouve les commandes par statuts multiples
     * 
     * @return array
     */
    public function findByStatuts(array $statuts): array
    {
        $placeholders = str_repeat('?,', count($statuts) - 1) . '?';
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as client_prenom, 
                   u.email as client_email,
                   u.telephone as client_telephone,
                   m.titre as menu_nom
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.statut IN ($placeholders)
            ORDER BY c.date_prestation ASC, c.created_at DESC
        ");
        $stmt->execute($statuts);
        return $stmt->fetchAll();
    }

    /**
     * Trouve les commandes par date de prestation
     * 
     * @return array
     */
    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   u.prenom as client_prenom, 
                   u.email as client_email,
                   m.titre as menu_nom
            FROM commande c
            LEFT JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            LEFT JOIN menu m ON c.menu_id = m.menu_id
            WHERE DATE(c.date_prestation) = ?
            AND c.statut != 'annulée'
            ORDER BY c.heure_livraison ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }
}
