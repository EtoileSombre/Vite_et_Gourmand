<?php

namespace App\Models;

use App\Core\Model;

/**
 * Modèle Materiel
 * Définition de l'entité - L'accès aux données se fait via MaterielRepository
 */
class Materiel extends Model
{
    protected $table = 'materiel';
    protected $primaryKey = 'materiel_id';
}
