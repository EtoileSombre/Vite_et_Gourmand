<?php

namespace App\Models;

use App\Core\Model;

//Définition de l'entité - L'accès aux données se fait via MenuRepository
class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
}
