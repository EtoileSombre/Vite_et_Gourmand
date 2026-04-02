<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Avis;
use PDO;
class AvisRepository implements AvisRepositoryInterface
{
    private PDO $db;
    private string $table = 'avis';
    private string $primaryKey = 'avis_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?Avis
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? Avis::fromArray($result) : null;
    }

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
        
        $stmt->bindValue(':minNote', $minNote, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE utilisateur_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }

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
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }

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

    public function findByCommandeAndUser(string $numeroCommande, int $userId): ?Avis
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE numero_commande = :numero_commande
            AND utilisateur_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            'numero_commande' => $numeroCommande,
            'user_id' => $userId
        ]);
        $result = $stmt->fetch();
        return $result ? Avis::fromArray($result) : null;
    }

    public function create(array $data): int
    {
        return $this->createAvis($data);
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = :$field";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " 
                WHERE {$this->primaryKey} = :pk_id";

        $data['pk_id'] = $id;
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
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
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }
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
        return array_map(fn($row) => Avis::fromArray($row), $stmt->fetchAll());
    }

    //Compte les avis par statut
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
}
