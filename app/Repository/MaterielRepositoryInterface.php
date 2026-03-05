<?php

namespace App\Repository;

interface MaterielRepositoryInterface
{
    public function findAllAvailable(): array;
    public function findById(int $id): ?array;
    public function findByIds(array $ids): array;
    public function findAll(): array;
    public function findByCategorie(string $categorie): array;
    public function getCategories(): array;
    public function getByCommande(string $numeroCommande): array;

    //Calcule le total de caution pour une commande
    public function getTotalCautionByCommande(string $numeroCommande): float;
    public function getEnAttenteRetour(): array;
}