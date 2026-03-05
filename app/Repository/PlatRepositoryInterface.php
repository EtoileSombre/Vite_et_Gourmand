<?php

namespace App\Repository;

interface PlatRepositoryInterface
{
    public function findAllPlats(?string $typePlat = null): array;
    public function findPlatById(int $id): ?array;
    public function findPlatById(int $id): ?array;

    //Compte le nombre de plats par type
    public function countByType(): array;
    public function getTypesPlat(): array;
    public function createPlat(array $data): ?int;
    public function updatePlat(int $id, array $data): bool;
    public function deletePlat(int $id): bool;
    public function getAllAllergenes(): array;
    public function getAllergenesForPlat(int $platId): array;
    public function getAllergenesForPlat(int $platId): array;

    //Associe des allergènes à un plat
    public function syncAllergenes(int $platId, array $allergeneIds): bool;
}
