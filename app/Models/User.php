<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'utilisateur_id';

    public static function findByEmail($email)
    {
        $model = new self();
        $stmt = $model->db->prepare('
            SELECT u.*, r.libelle as role 
            FROM utilisateur u 
            LEFT JOIN role r ON u.role_id = r.role_id 
            WHERE u.email = ?
        ');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createUser($data)
    {
        return $this->create($data);
    }

    public function toggleActive($userId, $actif)
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET est_actif = ? 
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$actif ? 1 : 0, $userId]);
    }

    /**
     * Récupère tous les utilisateurs avec leur rôle
     */
    public function findAllWithRole(): array
    {
        $stmt = $this->db->prepare('
            SELECT u.*, r.libelle as role_nom 
            FROM utilisateur u
            LEFT JOIN role r ON u.role_id = r.role_id
            ORDER BY u.created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Crée un employé avec mot de passe (ECF)
     * 
     * @return int|false ID de l'employé créé ou false
     */
    public function createEmployeWithPassword($email, $prenom, $nom, $password)
    {
        // Vérifier que l'email n'existe pas déjà
        if (self::findByEmail($email)) {
            return false;
        }
        
        $stmt = $this->db->prepare('
            INSERT INTO utilisateur (email, password, prenom, nom, role_id, actif) 
            VALUES (?, ?, ?, ?, 2, 1)
        ');
        
        if ($stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $prenom, $nom])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Désactive un compte utilisateur (ECF - départ employé)
     * 
     * @return bool
     */
    public function deactivate($utilisateurId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET actif = 0
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$utilisateurId]);
    }

    /**
     * Active un compte utilisateur
     * 
     * @return bool
     */
    public function activate($utilisateurId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE utilisateur 
            SET actif = 1
            WHERE utilisateur_id = ?
        ');
        return $stmt->execute([$utilisateurId]);
    }

    /**
     * Crée un employé sans mot de passe
     * Le mot de passe sera défini via le lien de réinitialisation
     * 
     * @return int|false ID de l'employé créé ou false
     */
    public function createEmploye($email, $prenom, $nom)
    {
        // Vérifier que l'email n'existe pas déjà
        if (self::findByEmail($email)) {
            return false;
        }

        // Générer un mot de passe temporaire aléatoire (qui ne sera jamais utilisé)
        $tempPassword = bin2hex(random_bytes(16));
        
        $stmt = $this->db->prepare('
            INSERT INTO utilisateur (email, password, prenom, nom, role_id, est_actif) 
            VALUES (?, ?, ?, ?, 2, 1)
        ');
        
        if ($stmt->execute([$email, password_hash($tempPassword, PASSWORD_DEFAULT), $prenom, $nom])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
}
