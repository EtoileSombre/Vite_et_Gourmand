<?php

namespace App\Models;

class Horaire
{
    private string $jour;
    private ?string $heureOuverture = null;
    private ?string $heureFermeture = null;
    private bool $ferme = false;
    private ?string $updatedAt = null;

    public function getJour(): string { return $this->jour; }
    public function setJour(string $jour): self { $this->jour = $jour; return $this; }

    public function getHeureOuverture(): ?string { return $this->heureOuverture; }
    public function setHeureOuverture(?string $heureOuverture): self { $this->heureOuverture = $heureOuverture; return $this; }

    public function getHeureFermeture(): ?string { return $this->heureFermeture; }
    public function setHeureFermeture(?string $heureFermeture): self { $this->heureFermeture = $heureFermeture; return $this; }

    public function isFerme(): bool { return $this->ferme; }
    public function setFerme(bool $ferme): self { $this->ferme = $ferme; return $this; }

    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function setUpdatedAt(?string $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public static function fromArray(array $data): self
    {
        $entity = new self();
        if (isset($data['jour'])) $entity->setJour($data['jour']);
        if (array_key_exists('heure_ouverture', $data)) $entity->setHeureOuverture($data['heure_ouverture']);
        if (array_key_exists('heure_fermeture', $data)) $entity->setHeureFermeture($data['heure_fermeture']);
        if (isset($data['ferme'])) $entity->setFerme((bool) $data['ferme']);
        if (isset($data['updated_at'])) $entity->setUpdatedAt($data['updated_at']);
        return $entity;
    }
}
