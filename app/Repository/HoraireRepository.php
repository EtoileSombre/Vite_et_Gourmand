<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Horaire;
use PDO;
class HoraireRepository implements HoraireRepositoryInterface
{
    private PDO $db;
    private string $table = 'horaire';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT jour, heure_ouverture, heure_fermeture, ferme, updated_at
            FROM {$this->table}
            ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')
        ");

        return array_map(fn($row) => Horaire::fromArray($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function findByJour(string $jour): ?Horaire
    {
        $stmt = $this->db->prepare("
            SELECT jour, heure_ouverture, heure_fermeture, ferme, updated_at
            FROM {$this->table}
            WHERE jour = :jour
        ");

        $stmt->execute(['jour' => $jour]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? Horaire::fromArray($result) : null;
    }
    public function updateHoraire(string $jour, ?string $heureOuverture, ?string $heureFermeture, bool $ferme): bool
    {
        $existing = $this->findByJour($jour);

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET heure_ouverture = :heure_ouverture,
                    heure_fermeture = :heure_fermeture,
                    ferme = :ferme
                WHERE jour = :jour
            ");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (jour, heure_ouverture, heure_fermeture, ferme)
                VALUES (:jour, :heure_ouverture, :heure_fermeture, :ferme)
            ");
        }

        return $stmt->execute([
            'jour' => $jour,
            'heure_ouverture' => $heureOuverture,
            'heure_fermeture' => $heureFermeture,
            'ferme' => $ferme ? 1 : 0
        ]);
    }
    public function initializeDefaultHoraires(): void
    {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $defaultOuverture = '10:00:00';
        $defaultFermeture = '22:00:00';

        foreach ($jours as $jour) {
            $existing = $this->findByJour($jour);
            if (!$existing) {
                $this->updateHoraire($jour, $defaultOuverture, $defaultFermeture, false);
            }
        }
    }
    public function getHorairesFormatted(): string
    {
        $horaires = $this->findAll();

        if (empty($horaires)) {
            return 'Lun–Dim 10h–22h';
        }

        // Regrouper les jours avec les mêmes horaires
        $groupes = [];
        foreach ($horaires as $h) {
            if ($h->isFerme()) {
                $cle = 'fermé';
            } else {
                $ouverture = substr($h->getHeureOuverture(), 0, 5);
                $fermeture = substr($h->getHeureFermeture(), 0, 5);
                $cle = $ouverture . '-' . $fermeture;
            }

            $groupes[$cle][] = $h->getJour();
        }

        // Formater l'affichage
        $lignes = [];
        foreach ($groupes as $cle => $jours) {
            if ($cle === 'fermé') {
                $lignes[] = implode(', ', $jours) . ' : Fermé';
            } else {
                [$ouverture, $fermeture] = explode('-', $cle);
                $lignes[] = $this->formatJoursRange($jours) . ' ' .
                            str_replace(':', 'h', $ouverture) . '–' .
                            str_replace(':', 'h', $fermeture);
            }
        }

        return implode(' • ', $lignes);
    }

    //Formate une plage de jours consécutifs (ex: Lundi, Mardi, Mercredi => Lun–Mer)
    private function formatJoursRange(array $jours): string
    {
        $joursComplets = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $joursAbreges = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

        if (count($jours) === 7) {
            return 'Lun–Dim';
        }

        if (count($jours) === 1) {
            $index = array_search($jours[0], $joursComplets);
            return $joursAbreges[$index];
        }

        // Vérifier si les jours sont consécutifs
        $indexes = array_map(function ($jour) use ($joursComplets) {
            return array_search($jour, $joursComplets);
        }, $jours);

        sort($indexes);
        $consecutif = true;
        for ($i = 0; $i < count($indexes) - 1; $i++) {
            if ($indexes[$i + 1] - $indexes[$i] !== 1) {
                $consecutif = false;
                break;
            }
        }

        if ($consecutif) {
            return $joursAbreges[$indexes[0]] . '–' . $joursAbreges[$indexes[count($indexes) - 1]];
        } else {
            return implode(', ', array_map(function ($jour) use ($joursComplets, $joursAbreges) {
                $index = array_search($jour, $joursComplets);
                return $joursAbreges[$index];
            }, $jours));
        }
    }
}
