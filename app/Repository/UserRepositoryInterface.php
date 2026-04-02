<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findAllWithRole(): array;
    public function createEmployeWithPassword(string $email, string $prenom, string $nom, string $password);
    public function toggleActive(int $userId, bool $actif): bool;
    public function deactivate(int $utilisateurId): bool;
}