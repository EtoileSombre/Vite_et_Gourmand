<?php

namespace App\Repository;

use App\Models\PasswordReset;

interface PasswordResetRepositoryInterface
{
    public function createToken(string $email, string $token, string $expiresAt): bool;
    public function findValidToken(string $token): ?PasswordReset;
    public function markTokenAsUsed(string $token): bool;
}