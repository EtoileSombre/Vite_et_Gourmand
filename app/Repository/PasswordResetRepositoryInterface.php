<?php

namespace App\Repository;

interface PasswordResetRepositoryInterface
{
    public function createToken(string $email, string $token, string $expiresAt): bool;
    public function findValidToken(string $token): ?array;
    public function markTokenAsUsed(string $token): bool;
}