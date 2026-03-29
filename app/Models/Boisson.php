<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Boisson
 * Définition de l'entité - L'accès aux données se fait via BoissonRepository
 */
class Boisson extends Model
{
    protected $table = 'boisson';
    protected $primaryKey = 'boisson_id';
}
