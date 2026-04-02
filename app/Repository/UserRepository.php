<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\User;
use PDO;
class UserRepository implements UserRepositoryInterface
{
    private PDO $db;
    private string $table = 'utilisateur';
    private string $primaryKey = 'utilisateur_id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return array_map(fn($row) => User::fromArray($row), $stmt->fetchAll());
    }
    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? User::fromArray($result) : null;
    }
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('
            SELECT u.*, r.libelle as role
            FROM utilisateur u
            LEFT JOIN role r ON u.role_id = r.role_id
            WHERE u.email = :email
            LIMIT 1
        ');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ? User::fromArray($user) : null;
    }
    public function findAllWithRole(): array
    {
        $stmt = $this->db->prepare('
            SELECT u.*, r.libelle as role_nom 
            FROM utilisateur u
            LEFT JOIN role r ON u.role_id = r.role_id
            ORDER BY u.created_at DESC
        ');
        $stmt->execute();
        return array_map(fn($row) => User::fromArray($row), $stmt->fetchAll());
    }
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":$field", $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return (int) $this->db->lastInsertId();
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
    public function createEmployeWithPassword(string $email, string $prenom, string $nom, string $password)
    {
        // Vérifier que l'email n'existe pas déjà
        if ($this->findByEmail($email)) {
            return false;
        }
        
        $stmt = $this->db->prepare('
            INSERT INTO utilisateur (email, password, prenom, nom, role_id, actif) 
            VALUES (?, ?, ?, ?, 2, 1)
        ');
        
        if ($stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $prenom, $nom])) {
            return (int) $this->db->lastInsertId();
        }
        
        return false;
    }

    //Active ou désactive un utilisateur
    public function toggleActive(int $userId, bool $actif): bool
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET actif = ? 
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$actif ? 1 : 0, $userId]);
    }
    public function deactivate(int $utilisateurId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET actif = 0
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$utilisateurId]);
    }

    //Réactive un compte utilisateur
    public function activate(int $utilisateurId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET actif = 1
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$utilisateurId]);
    }
}