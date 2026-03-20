<?php

namespace App\Repository;

interface BoissonRepositoryInterface
{
    public function findAllAvailable(): array;
    public function findById(int $id): ?array;

    public function findByIds(array $ids): array;
    public function findAll(): array;
    public function findByType(string $type): array;
    public function getTypes(): array;
    public function getByCommande(string $numeroCommande): array;

    // Calcule le total des boissons pour une commande
    public function getTotalByCommande(string $numeroCommande): float;

    //Ajoute une boisson à une commande
    public function addBoissonToCommande(string $numeroCommande, int $boissonId, int $quantite, float $prixUnitaire): bool;
}