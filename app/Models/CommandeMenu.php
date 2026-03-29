<?php

namespace App\Models;

use App\Core\Model;

//L'accès aux données se fait via CommandeMenuRepository
class CommandeMenu extends Model
{
    protected $table = 'commande_menu';
    protected $primaryKey = 'commande_menu_id';
}
