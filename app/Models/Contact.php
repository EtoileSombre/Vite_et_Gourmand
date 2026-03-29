<?php

namespace App\Models;

use App\Core\Model;

// L'accès aux données se fait via ContactRepository
class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'contact_id';
}
