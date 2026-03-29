<?php

namespace App\Models;

use App\Core\Model;

// L'accès aux données se fait via AvisRepository
class Avis extends Model
{
    protected $table = 'avis';
    protected $primaryKey = 'avis_id';
}
