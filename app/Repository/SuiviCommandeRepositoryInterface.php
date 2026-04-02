<?php

namespace App\Repository;

use App\Models\SuiviCommande;

interface SuiviCommandeRepositoryInterface
{
    public function enregistrerChangement(
        string $numeroCommande,
        ?string $ancienStatut,
        string $nouveauStatut,
        ?int $employeId = null,
        ?string $commentaire = null
    ): bool;

    //Récupérer l'historique complet d'une commande
    public function getHistorique(string $numeroCommande): array;

    //Récupérer le dernier changement de statut d'une commande
    public function getDernierChangement(string $numeroCommande): ?SuiviCommande;
    public function countChangements(string $numeroCommande): int;
}
