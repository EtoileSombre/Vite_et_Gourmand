<?php

namespace App\Models;

use App\Core\Model;

//L'accès aux données se fait via SuiviCommandeRepository
class SuiviCommande extends Model
{
    protected $table = 'suivi_commande';
    protected $primaryKey = 'suivi_id';
}
