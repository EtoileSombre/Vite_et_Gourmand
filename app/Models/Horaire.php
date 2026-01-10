<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use PDO;

class Horaire extends Model
{
    protected $table = 'horaire';

    /**
     * Récupère tous les horaires pour tous les jours de la semaine
     */
    public static function findAllHoraires(): array
    {
        $db = Database::getInstance();
        
        $stmt = $db->query("
            SELECT jour, heure_ouverture, heure_fermeture, ferme, updated_at
            FROM horaire
            ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les horaires d'un jour spécifique
     */
    public static function findByJour(string $jour): ?array
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT jour, heure_ouverture, heure_fermeture, ferme, updated_at
            FROM horaire
            WHERE jour = :jour
        ");
        
        $stmt->execute(['jour' => $jour]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /**
     * Met à jour les horaires d'un jour
     */
    public static function updateHoraire(string $jour, ?string $heureOuverture, ?string $heureFermeture, bool $ferme): bool
    {
        $db = Database::getInstance();
        
        // Si le jour n'existe pas, on l'insère
        $existing = static::findByJour($jour);
        
        if ($existing) {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE horaire
                SET heure_ouverture = :heure_ouverture,
                    heure_fermeture = :heure_fermeture,
                    ferme = :ferme
                WHERE jour = :jour
            ");
        } else {
            // INSERT
            $stmt = $db->prepare("
                INSERT INTO horaire (jour, heure_ouverture, heure_fermeture, ferme)
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

    /**
     * Initialise tous les jours de la semaine s'ils n'existent pas
     */
    public static function initializeDefaultHoraires(): void
    {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $defaultOuverture = '10:00:00';
        $defaultFermeture = '22:00:00';
        
        foreach ($jours as $jour) {
            $existing = static::findByJour($jour);
            if (!$existing) {
                static::updateHoraire($jour, $defaultOuverture, $defaultFermeture, false);
            }
        }
    }

    /**
     * Récupère les horaires formatés pour l'affichage dans le footer
     */
    public static function getHorairesFormatted(): string
    {
        $horaires = static::findAllHoraires();
        
        if (empty($horaires)) {
            return 'Lun–Dim 10h–22h';
        }
        
        // Regrouper les jours avec les mêmes horaires
        $groupes = [];
        foreach ($horaires as $h) {
            if ($h['ferme']) {
                $cle = 'fermé';
            } else {
                $ouverture = substr($h['heure_ouverture'], 0, 5);
                $fermeture = substr($h['heure_fermeture'], 0, 5);
                $cle = $ouverture . '-' . $fermeture;
            }
            
            $groupes[$cle][] = $h['jour'];
        }
        
        // Formater l'affichage
        $lignes = [];
        foreach ($groupes as $cle => $jours) {
            if ($cle === 'fermé') {
                $lignes[] = implode(', ', $jours) . ' : Fermé';
            } else {
                [$ouverture, $fermeture] = explode('-', $cle);
                $lignes[] = static::formatJoursRange($jours) . ' ' . 
                            str_replace(':', 'h', $ouverture) . '–' . 
                            str_replace(':', 'h', $fermeture);
            }
        }
        
        return implode(' • ', $lignes);
    }

    /**
     * Formate une plage de jours consécutifs (ex: Lundi, Mardi, Mercredi => Lun–Mer)
     */
    private static function formatJoursRange(array $jours): string
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
        $indexes = array_map(function($jour) use ($joursComplets) {
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
            // Jours non consécutifs, afficher tous
            return implode(', ', array_map(function($jour) use ($joursComplets, $joursAbreges) {
                $index = array_search($jour, $joursComplets);
                return $joursAbreges[$index];
            }, $jours));
        }
    }
}
