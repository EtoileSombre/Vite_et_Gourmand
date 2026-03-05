<?php

namespace App\Repository;

interface MenuRepositoryInterface extends RepositoryInterface
{
    public function findActive(): array;
    public function findActiveWithPhotos(): array;
    public function findActiveById(int $id): ?array;
    public function getPlatsForMenu(int $menuId): array;
    public function getPlatIdsForMenu(int $menuId): array;

    //Associe des plats à un menu
    public function syncPlats(int $menuId, array $platIds): bool;
    public function getAllThemes(): array;
    public function getAllRegimes(): array;
    public function getPhotosMenu(int $menuId): array;

    //Filtre les menus selon les critères spécifiés
    public function findFiltered(array $filters): array;
}