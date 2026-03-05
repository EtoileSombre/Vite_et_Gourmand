<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class SuiviCommandeRepository implements SuiviCommandeRepositoryInterface
{
    private PDO $db;
    private string $table = 'suivi_commande';
    private string $primaryKey = 'suivi_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Enregistre un changement de statut pour une commande
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
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // Compte le nombre de changements de statut pour une commande
    public function countChangements(string $numeroCommande): int
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE numero_commande = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$numeroCommande]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}