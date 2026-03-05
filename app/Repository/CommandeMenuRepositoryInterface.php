<?php

namespace App\Repository;

/**
 * Interface pour le repository des lignes de commande (CommandeMenu)
 */
interface CommandeMenuRepositoryInterface
{
    //Récupérer tous les menus d'une commande

    public function findByCommande(string $numeroCommande): array;
    public function addMenuToCommande(
        string $numeroCommande,
        int $menuId,
        int $nombrePersonne,
        float $prixParPersonne,
        float $reduction = 0
    ): bool;
    public function updateLigne(
        int $commandeMenuId,
        int $nombrePersonne,
        float $prixParPersonne,
        float $reduction = 0
    ): bool;

    //Supprimer une ligne de commande
    public function deleteLigne(int $commandeMenuId): bool;
    public function getTotalMenus(string $numeroCommande): float;
    public function getTotalPersonnes(string $numeroCommande): int;

    //Récupérer une ligne de commande par son ID
    public function findById(int $commandeMenuId): ?array;
    public function updateQuantite(string $numeroCommande, int $menuId, int $nombrePersonne): bool;
}