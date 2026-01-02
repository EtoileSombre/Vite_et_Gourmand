<?php

namespace App\Models;

use App\Core\Model;

class SuiviCommande extends Model
{
    protected $table = 'suivi_commande';
    protected $primaryKey = 'suivi_id';

    /**
     * Enregistrer un changement de statut de commande
     */
    public function enregistrerChangement(
        string $numeroCommande,
        ?string $ancienStatut,
        string $nouveauStatut,
        ?int $employeId = null,
        ?string $commentaire = null
    ): bool {
        // Vérifier s'il n'y a pas déjà un enregistrement identique récent (moins de 5 secondes)
        // On vérifie juste le statut et la commande, peu importe l'employé
        $sqlCheck = "SELECT COUNT(*) as count FROM {$this->table} 
                     WHERE numero_commande = ? 
                     AND nouveau_statut = ? 
                     AND date_changement > DATE_SUB(NOW(), INTERVAL 5 SECOND)";
        
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([$numeroCommande, $nouveauStatut]);
        $result = $stmtCheck->fetch(\PDO::FETCH_ASSOC);
        
        // Si un enregistrement identique existe déjà récemment, ne pas créer de doublon
        if ($result['count'] > 0) {
            return true; // Retourner true car ce n'est pas une erreur
        }
        
        $sql = "INSERT INTO {$this->table} 
                (numero_commande, ancien_statut, nouveau_statut, employe_id, commentaire) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $numeroCommande,
            $ancienStatut,
            $nouveauStatut,
            $employeId,
            $commentaire
        ]);
    }

    /**
     * Récupérer l'historique complet d'une commande
     */
    public function getHistorique(string $numeroCommande): array
    {
        $sql = "SELECT 
                    s.*,
                    u.prenom as employe_prenom,
                    u.nom as employe_nom
                FROM {$this->table} s
                LEFT JOIN utilisateur u ON s.employe_id = u.utilisateur_id
                WHERE s.numero_commande = ?
                ORDER BY s.date_changement ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numeroCommande]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer le dernier changement de statut d'une commande
     */
    public function getDernierChangement(string $numeroCommande): ?array
    {
        $sql = "SELECT 
                    s.*,
                    u.prenom as employe_prenom,
                    u.nom as employe_nom
                FROM {$this->table} s
                LEFT JOIN utilisateur u ON s.employe_id = u.utilisateur_id
                WHERE s.numero_commande = ?
                ORDER BY s.date_changement DESC
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Compter le nombre de changements pour une commande
     */
    public function countChangements(string $numeroCommande): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE numero_commande = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}
