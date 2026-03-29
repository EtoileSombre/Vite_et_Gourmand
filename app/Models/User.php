<?php

namespace App\Models;

use App\Core\Model;

//L'accès aux données se fait via UserRepository
class User extends Model
{
    protected $table = 'utilisateur';
    protected $primaryKey = 'utilisateur_id';
}
