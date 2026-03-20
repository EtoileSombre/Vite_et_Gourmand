<?php

namespace App\Repository;

interface CommandeRepositoryInterface extends RepositoryInterface
{
    public function findByUser(int $userId): array;
    public function findByNumero(string $numeroCommande): ?array;
    public function findWithDetails(string $numeroCommande): ?array;
    public function findAllWithDetails(): array;
    public function updateStatut(string $numeroCommande, string $statut): bool;
    public function updateByNumero(string $numeroCommande, array $data): bool;
}