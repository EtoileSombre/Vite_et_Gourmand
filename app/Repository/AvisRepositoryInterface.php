<?php

namespace App\Repository;

interface AvisRepositoryInterface extends RepositoryInterface
{
    public function findValidatedWithGoodRating(int $minNote = 4, int $limit = 6): array;
    public function findByUser(int $userId): array;
    public function findPending(): array;
    public function createAvis(array $data): int;
    public function findByCommandeAndUser(string $numeroCommande, int $userId): ?array;
}