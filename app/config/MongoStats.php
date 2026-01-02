<?php
// Helper MongoDB pour les statistiques

namespace App\Config;

use Exception;
use MongoDB\BSON\UTCDateTime;

class MongoStats
{
    private $mongodb;
    private $collections;

    public function __construct()
    {
        global $mongodb, $mongoCollections;
        $this->mongodb = $mongodb;
        $this->collections = $mongoCollections;
    }

    public function isAvailable(): bool
    {
        return $this->mongodb !== null;
    }

    public function logMenuView(int $menuId, array $menuData = []): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        try {
            $this->collections['menu_views']->insertOne([
                'menu_id' => $menuId,
                'menu_titre' => $menuData['titre'] ?? null,
                'timestamp' => new UTCDateTime(),
                'date' => date('Y-m-d'),
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logMenuView : " . $e->getMessage());
            return false;
        }
    }

    public function logUserActivity(string $action, ?int $utilisateurId = null, array $details = []): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        try {
            $this->collections['user_activity']->insertOne([
                'action' => $action,
                'utilisateur_id' => $utilisateurId,
                'details' => $details,
                'timestamp' => new UTCDateTime(),
                'date' => date('Y-m-d'),
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logUserActivity : " . $e->getMessage());
            return false;
        }
    }

     /* Enregistre une stat de commande*/
    public function logCommande(string $numeroCommande, array $commandeData): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        try {
            $this->collections['commande_stats']->insertOne([
                'numero_commande' => $numeroCommande,
                'menu_id' => $commandeData['menu_id'] ?? null,
                'prix_total' => $commandeData['prix_total'] ?? 0,
                'nombre_personne' => $commandeData['nombre_personne'] ?? 0,
                'statut' => $commandeData['statut'] ?? 'en attente',
                'timestamp' => new UTCDateTime(),
                'date' => date('Y-m-d')
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logCommande : " . $e->getMessage());
            return false;
        }
    }

     /* Récupère les menus les plus vus avec période*/
    public function getTopMenusByPeriod(int $limit = 10, int $jours = 30): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            $dateDebut = date('Y-m-d', strtotime("-{$jours} days"));

            $pipeline = [
                ['$match' => ['date' => ['$gte' => $dateDebut]]],
                ['$group' => [
                    '_id' => '$menu_id',
                    'menu_titre' => ['$first' => '$menu_titre'],
                    'total_vues' => ['$sum' => 1]
                ]],
                ['$sort' => ['total_vues' => -1]],
                ['$limit' => $limit]
            ];

            $result = $this->collections['menu_views']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    'menu_id' => $row['_id'],
                    'titre' => $row['menu_titre'] ?? null,
                    'count' => $row['total_vues']
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getTopMenusByPeriod : " . $e->getMessage());
            return [];
        }
    }

     /* Récupère le nombre de vues par jour (derniers 7 jours*/
    public function getViewsPerDay(int $jours = 7): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            $dateDebut = date('Y-m-d', strtotime("-{$jours} days"));

            $pipeline = [
                ['$match' => ['date' => ['$gte' => $dateDebut]]],
                ['$group' => [
                    '_id' => '$date',
                    'total_vues' => ['$sum' => 1]
                ]],
                ['$sort' => ['_id' => 1]]
            ];

            $result = $this->collections['menu_views']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    'date' => $row['_id'],
                    'count' => $row['total_vues']
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getViewsPerDay : " . $e->getMessage());
            return [];
        }
    }

     /* Récupère les statistiques globales*/
    public function getGlobalStats(): array
    {
        if (!$this->isAvailable()) {
            return [
                'total_vues_menus' => 0,
                'total_activites' => 0,
                'total_commandes' => 0,
                'total_avis' => 0
            ];
        }

        try {
            return [
                'total_vues_menus' => $this->collections['menu_views']->countDocuments(),
                'total_activites' => $this->collections['user_activity']->countDocuments(),
                'total_commandes' => $this->collections['commande_stats']->countDocuments(),
                'total_avis' => 0 // À implémenter si besoin
            ];
        } catch (Exception $e) {
            error_log("Erreur getGlobalStats : " . $e->getMessage());
            return [
                'total_vues_menus' => 0,
                'total_activites' => 0,
                'total_commandes' => 0,
                'total_avis' => 0
            ];
        }
    }

    /*Récupère le TOP menus avec vues connectés/visiteurs*/
    public function getTopMenus(int $limit = 5): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            $pipeline = [
                ['$group' => [
                    '_id' => '$menu_id',
                    'titre' => ['$first' => '$menu_titre'],
                    'total_vues' => ['$sum' => 1],
                    'vues_connectes' => ['$sum' => ['$cond' => [['$gt' => ['$utilisateur_id', null]], 1, 0]]],
                    'vues_visiteurs' => ['$sum' => ['$cond' => [['$eq' => ['$utilisateur_id', null]], 1, 0]]]
                ]],
                ['$sort' => ['total_vues' => -1]],
                ['$limit' => $limit]
            ];

            $result = $this->collections['menu_views']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    '_id' => $row['_id'],
                    'titre' => $row['titre'] ?? null,
                    'total_vues' => $row['total_vues'],
                    'vues_connectes' => $row['vues_connectes'],
                    'vues_visiteurs' => $row['vues_visiteurs']
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getTopMenus : " . $e->getMessage());
            return [];
        }
    }

    /*Nombre de commandes par menu (MongoDB)*/
    public function getCommandesParMenu(?int $menuId = null, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            // Construire le filtre
            $match = [];
            if ($menuId) {
                $match['menu_id'] = $menuId;
            }
            if ($dateDebut) {
                $match['date'] = ['$gte' => $dateDebut];
            }
            if ($dateFin) {
                if (isset($match['date'])) {
                    $match['date']['$lte'] = $dateFin;
                } else {
                    $match['date'] = ['$lte' => $dateFin];
                }
            }

            $pipeline = [
                ['$match' => $match],
                ['$group' => [
                    '_id' => '$menu_id',
                    'nombre_commandes' => ['$sum' => 1],
                    'total_personnes' => ['$sum' => '$nombre_personne']
                ]],
                ['$sort' => ['nombre_commandes' => -1]]
            ];

            $result = $this->collections['commande_stats']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    '_id' => $row['_id'],
                    'nombre_commandes' => $row['nombre_commandes'],
                    'total_personnes' => $row['total_personnes'] ?? 0
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getCommandesParMenu : " . $e->getMessage());
            return [];
        }
    }

    /*Chiffre d'affaires par menu avec filtres (MongoDB)*/
    public function getCAParMenu(?int $menuId = null, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            // Construire le filtre
            $match = [];
            if ($menuId) {
                $match['menu_id'] = $menuId;
            }
            if ($dateDebut) {
                $match['date'] = ['$gte' => $dateDebut];
            }
            if ($dateFin) {
                if (isset($match['date'])) {
                    $match['date']['$lte'] = $dateFin;
                } else {
                    $match['date'] = ['$lte' => $dateFin];
                }
            }

            $pipeline = [
                ['$match' => $match],
                ['$group' => [
                    '_id' => '$menu_id',
                    'chiffre_affaires' => ['$sum' => '$prix_total'],
                    'nombre_commandes' => ['$sum' => 1],
                    'total_personnes' => ['$sum' => '$nombre_personne']
                ]],
                ['$sort' => ['chiffre_affaires' => -1]]
            ];

            $result = $this->collections['commande_stats']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $data[] = [
                    '_id' => $row['_id'],
                    'chiffre_affaires' => (float)$row['chiffre_affaires'],
                    'nombre_commandes' => $row['nombre_commandes'],
                    'total_personnes' => $row['total_personnes'] ?? 0
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getCAParMenu : " . $e->getMessage());
            return [];
        }
    }
}
