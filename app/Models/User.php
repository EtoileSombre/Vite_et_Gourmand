<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'utilisateur';

    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email
     * @return array|false
     */
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

    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data
     * @return int ID de l'utilisateur créé
     */
    public function createUser($data)
    {
        return $this->create($data);
    }
}
