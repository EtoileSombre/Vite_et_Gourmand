<?php

namespace App\Models;

use App\Core\Model;

class Avis extends Model
{
    protected $table = 'avis';
    protected $primaryKey = 'avis_id';

    // Avis validés avec note minimum (pour affichage accueil)
    public function findValidatedWithGoodRating(int $minNote = 4, int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT a.note, a.description, a.created_at,
                   u.prenom, u.nom
            FROM {$this->table} a
            INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.statut = 'publie'
            AND a.note >= :minNote
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':minNote', $minNote, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE utilisateur_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les avis en attente de validation
     * 
     * @return array
     */
    public function findPending(): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.prenom, u.nom, u.email
            FROM {$this->table} a
            INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.statut = 'en_attente'
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Crée un nouvel avis
     * 
     * @param array $data
     * @return int ID de l'avis créé
     */
    public function createAvis(array $data): int
    {
        // Vérifier si un numéro de commande est fourni
        if (isset($data['numero_commande'])) {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (utilisateur_id, numero_commande, note, description, statut, created_at)
                VALUES (:user_id, :numero_commande, :note, :description, 'en_attente', NOW())
            ");
            
            $stmt->execute([
                'user_id' => $data['utilisateur_id'],
                'numero_commande' => $data['numero_commande'],
                'note' => $data['note'],
                'description' => $data['description']
            ]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (utilisateur_id, note, description, statut, created_at)
                VALUES (:user_id, :note, :description, 'en_attente', NOW())
            ");
            
            $stmt->execute([
                'user_id' => $data['utilisateur_id'],
                'note' => $data['note'],
                'description' => $data['description']
            ]);
        }
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour le statut d'un avis
     * 
     * @param int $id
     * @param string $statut
     * @return bool
     */
    public function updateStatus(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET statut = :statut 
            WHERE {$this->primaryKey} = :id
        ");
        
        return $stmt->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }

    /**
     * Trouve les avis par statut
     * 
     * @param string $statut
     * @return array
     */
    public function findByStatut(string $statut): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.prenom as utilisateur_prenom,
                   u.email as utilisateur_email
            FROM {$this->table} a
            LEFT JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.statut = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$statut]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les avis avec détails complets (pour employés)
     * 
     * @return array
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   c.numero_commande
            FROM {$this->table} a
            LEFT JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            LEFT JOIN commande c ON a.numero_commande = c.numero_commande
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les avis par statut avec détails complets
     * 
     * @param string $statut
     * @return array
     */
    public function findByStatutWithDetails(string $statut): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   u.prenom as utilisateur_prenom,
                   u.nom as utilisateur_nom,
                   u.email as utilisateur_email,
                   c.numero_commande
            FROM {$this->table} a
            LEFT JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            LEFT JOIN commande c ON a.numero_commande = c.numero_commande
            WHERE a.statut = :statut
            ORDER BY a.created_at DESC
        ");
        $stmt->execute(['statut' => $statut]);
        return $stmt->fetchAll();
    }

    /**
     * Compte les avis par statut
     * 
     * @param string $statut
     * @return int
     */
    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM {$this->table}
            WHERE statut = :statut
        ");
        $stmt->execute(['statut' => $statut]);
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Vérifie si un utilisateur a déjà donné un avis pour une commande
     * 
     * @param string $numeroCommande
     * @param int $userId
     * @return array|null
     */
    public function findByCommandeAndUser(string $numeroCommande, int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM {$this->table}
            WHERE numero_commande = :numero_commande
            AND utilisateur_id = :user_id
        ");
        $stmt->execute([
            'numero_commande' => $numeroCommande,
            'user_id' => $userId
        ]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
