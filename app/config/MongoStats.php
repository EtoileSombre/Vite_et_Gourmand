<?php

namespace App\Config;

use Exception;
use MongoDB\Driver\Exception\Exception as MongoException;

/**
 * Classe de statistiques MongoDB
 * @psalm-suppress UndefinedClass
 */
class MongoStats
{
    private $mongodb;
    private $collections;

    public function __construct()
    {
        try {
            global $mongodb, $mongoCollections;
            if ($mongodb !== null && !empty($mongoCollections)) {
                $this->mongodb = $mongodb;
                $this->collections = $mongoCollections;
                return;
            }

            $vendorPath = __DIR__ . '/../vendor/autoload.php';
            if (!file_exists($vendorPath)) {
                $vendorPath = __DIR__ . '/../../vendor/autoload.php';
            }
            if (file_exists($vendorPath)) {
                require_once $vendorPath;
            }
            
            $mongoUri = getenv('MONGO_URI') ?: 'mongodb://vgroot:vgrootpass@mongo:27017';
            $mongoDbName = getenv('MONGO_DATABASE') ?: 'vg';
            
            $mongoClient = new \MongoDB\Client(
                $mongoUri,
                [],
                [
                    'typeMap' => [
                        'root' => 'array',
                        'document' => 'array',
                        'array' => 'array'
                    ]
                ]
            );

            $this->mongodb = $mongoClient->$mongoDbName;

            $this->collections = [
                'menu_views' => $this->mongodb->menu_views,
                'user_activity' => $this->mongodb->user_activity,
                'commande_stats' => $this->mongodb->commande_stats,
                'avis_analytics' => $this->mongodb->avis_analytics
            ];

        } catch (Exception $e) {
            error_log("Erreur initialisation MongoStats : " . $e->getMessage());
            $this->mongodb = null;
            $this->collections = [];
        }
    }

    public function isAvailable(): bool
    {
        return $this->mongodb !== null && !empty($this->collections);
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
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
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
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
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
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d')
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logCommande : " . $e->getMessage());
            return false;
        }
    }
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
            
            // Filtrage sur timestamp (UTCDateTime) pour robustesse
            if ($dateDebut) {
                $timestampDebut = new \MongoDB\BSON\UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
                $match['timestamp'] = ['$gte' => $timestampDebut];
            }
            if ($dateFin) {
                $timestampFin = new \MongoDB\BSON\UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
                if (isset($match['timestamp'])) {
                    $match['timestamp']['$lte'] = $timestampFin;
                } else {
                    $match['timestamp'] = ['$lte' => $timestampFin];
                }
            }

            $pipeline = [];
            
            if (!empty($match)) {
                $pipeline[] = ['$match' => $match];
            }
            
            $pipeline[] = ['$group' => [
                '_id' => '$menu_id',
                'nombre_commandes' => ['$sum' => 1],
                'total_personnes' => ['$sum' => '$nombre_personne']
            ]];
            $pipeline[] = ['$sort' => ['nombre_commandes' => -1]];

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

    /* Chiffre d'affaires par menu avec filtres */
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
            
            // Filtrage sur timestamp (UTCDateTime) pour robustesse
            if ($dateDebut) {
                $timestampDebut = new \MongoDB\BSON\UTCDateTime(strtotime($dateDebut . ' 00:00:00') * 1000);
                $match['timestamp'] = ['$gte' => $timestampDebut];
            }
            if ($dateFin) {
                $timestampFin = new \MongoDB\BSON\UTCDateTime(strtotime($dateFin . ' 23:59:59') * 1000);
                if (isset($match['timestamp'])) {
                    $match['timestamp']['$lte'] = $timestampFin;
                } else {
                    $match['timestamp'] = ['$lte' => $timestampFin];
                }
            }

            $pipeline = [];
            
            // N'ajouter $match que s'il y a des filtres
            if (!empty($match)) {
                $pipeline[] = ['$match' => $match];
            }
            
            $pipeline[] = ['$group' => [
                '_id' => '$menu_id',
                'chiffre_affaires' => ['$sum' => '$prix_total'],
                'nombre_commandes' => ['$sum' => 1],
                'total_personnes' => ['$sum' => '$nombre_personne']
            ]];
            $pipeline[] = ['$addFields' => [
                // Sécurisation : division par zéro protégée avec $cond
                'montant_moyen' => [
                    '$cond' => [
                        ['$gt' => ['$nombre_commandes', 0]],
                        ['$divide' => ['$chiffre_affaires', '$nombre_commandes']],
                        0
                    ]
                ]
            ]];
            $pipeline[] = ['$sort' => ['chiffre_affaires' => -1]];

            $result = $this->collections['commande_stats']->aggregate($pipeline);
            $data = [];
            foreach ($result as $row) {
                $caTTC = (float)$row['chiffre_affaires'];
                $caHT = round($caTTC / 1.10, 2); // TVA 10%
                $montantTVA = round($caTTC - $caHT, 2);
                $moyenneTTC = round($row['montant_moyen'] ?? 0, 2);
                $moyenneHT = round($moyenneTTC / 1.10, 2);
                
                $data[] = [
                    '_id' => $row['_id'],
                    'chiffre_affaires' => $caTTC,
                    'ca_ht' => $caHT,
                    'ca_ttc' => $caTTC,
                    'tva' => $montantTVA,
                    'nombre_commandes' => $row['nombre_commandes'],
                    'total_personnes' => $row['total_personnes'] ?? 0,
                    'montant_moyen' => $moyenneTTC,
                    'montant_moyen_ht' => $moyenneHT,
                    'montant_moyen_ttc' => $moyenneTTC
                ];
            }
            return $data;

        } catch (Exception $e) {
            error_log("Erreur getCAParMenu : " . $e->getMessage());
            return [];
        }
    }
}
