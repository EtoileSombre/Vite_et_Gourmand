<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Plat
 * Définition de l'entité - L'accès aux données se fait via PlatRepository
 */
class Plat extends Model
{
    protected $table = 'plat';
    protected $primaryKey = 'plat_id';
}
