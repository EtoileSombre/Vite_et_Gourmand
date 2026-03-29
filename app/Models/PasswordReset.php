<?php

namespace App\Models;

use App\Core\Model;

//L'accès aux données se fait via PasswordResetRepository
class PasswordReset extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
}
