<?php

namespace App\Repository;

use App\Models\Horaire;

interface HoraireRepositoryInterface
{
    public function findAll(): array;
    public function findByJour(string $jour): ?Horaire;
    public function updateHoraire(string $jour, ?string $heureOuverture, ?string $heureFermeture, bool $ferme): bool;
    public function initializeDefaultHoraires(): void;
    public function getHorairesFormatted(): string;
}
