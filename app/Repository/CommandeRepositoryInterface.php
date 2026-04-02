<?php

namespace App\Repository;

use App\Models\Commande;

interface CommandeRepositoryInterface extends RepositoryInterface
{
    public function findByUser(int $userId): array;
    public function findByNumero(string $numeroCommande): ?Commande;
    public function findWithDetails(string $numeroCommande): ?Commande;
    public function findAllWithDetails(): array;
    public function updateStatut(string $numeroCommande, string $statut): bool;
    public function updateByNumero(string $numeroCommande, array $data): bool;
}