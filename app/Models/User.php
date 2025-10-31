<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'utilisateurs';

    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email
     * @return array|false
     */
    public static function findByEmail(string $email)
    {
        $model = new self();
        $stmt = $model->db->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data
     * @return int ID de l'utilisateur créé
     */
    public function createUser(array $data): int
    {
        return $this->create($data);
    }
}
