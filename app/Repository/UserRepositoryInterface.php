<?php

namespace App\Repository;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?array;
    public function findAllWithRole(): array;
    public function createEmployeWithPassword(string $email, string $prenom, string $nom, string $password);
    public function toggleActive(int $userId, bool $actif): bool;
    public function deactivate(int $utilisateurId): bool;
}