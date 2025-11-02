<?php

namespace App\Helpers;

use MongoDB\Client;
use Exception;

/**
 * Helper pour logger les événements dans MongoDB
 */
class MongoLogger
{
    private static $client = null;
    private static $db = null;
    
    /**
     * Initialise la connexion MongoDB
     */
    private static function connect()
    {
        if (self::$client === null) {
            try {
                self::$client = new Client(
                    "mongodb://vgroot:vgrootpass@mongo:27017",
                    [],
                    [
                        'typeMap' => [
                            'root' => 'array',
                            'document' => 'array',
                            'array' => 'array'
                        ]
                    ]
                );
                self::$db = self::$client->vg;
            } catch (Exception $e) {
                error_log("MongoDB connexion error: " . $e->getMessage());
                return false;
            }
        }
        return self::$db !== null;
    }
    
    /**
     * Log une consultation de menu
     * 
     * @param int $menuId ID du menu consulté
     * @param int|null $userId ID de l'utilisateur (null si non connecté)
     * @param string $menuTitre Titre du menu
     */
    public static function logMenuView(int $menuId, ?int $userId, string $menuTitre)
    {
        if (!self::connect()) return;
        
        try {
            self::$db->menu_views->insertOne([
                'menu_id' => $menuId,
                'menu_titre' => $menuTitre,
                'user_id' => $userId,
                'user_type' => $userId ? 'connecté' : 'visiteur',
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'heure' => date('H:i:s')
            ]);
        } catch (Exception $e) {
            error_log("MongoDB logMenuView error: " . $e->getMessage());
        }
    }
    
    /**
     * Log une création de commande
     * 
     * @param string $numeroCommande Numéro de commande
     * @param int $userId ID utilisateur
     * @param int $menuId ID du menu
     * @param int $nombrePersonnes Nombre de personnes
     * @param float $montantTotal Montant total
     */
    public static function logCommande(string $numeroCommande, int $userId, int $menuId, int $nombrePersonnes, float $montantTotal)
    {
        if (!self::connect()) return;
        
        try {
            self::$db->commande_stats->insertOne([
                'numero_commande' => $numeroCommande,
                'user_id' => $userId,
                'menu_id' => $menuId,
                'nombre_personnes' => $nombrePersonnes,
                'montant_total' => $montantTotal,
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'heure' => date('H:i:s'),
                'jour_semaine' => date('l'),
                'mois' => date('F Y')
            ]);
        } catch (Exception $e) {
            error_log("MongoDB logCommande error: " . $e->getMessage());
        }
    }
    
    /**
     * Log une activité utilisateur
     * 
     * @param string $action Type d'action (login, logout, view_page, etc.)
     * @param int|null $userId ID utilisateur
     * @param array $details Détails supplémentaires
     */
    public static function logUserActivity(string $action, ?int $userId, array $details = [])
    {
        if (!self::connect()) return;
        
        try {
            self::$db->user_activity->insertOne([
                'action' => $action,
                'user_id' => $userId,
                'details' => $details,
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'heure' => date('H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("MongoDB logUserActivity error: " . $e->getMessage());
        }
    }
    
    /**
     * Log la soumission d'un avis
     * 
     * @param int $avisId ID de l'avis
     * @param int $userId ID utilisateur
     * @param int $note Note donnée
     */
    public static function logAvis(int $avisId, int $userId, int $note)
    {
        if (!self::connect()) return;
        
        try {
            self::$db->avis_analytics->insertOne([
                'avis_id' => $avisId,
                'user_id' => $userId,
                'note' => $note,
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'mois' => date('F Y')
            ]);
        } catch (Exception $e) {
            error_log("MongoDB logAvis error: " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les statistiques des menus les plus consultés
     * 
     * @param int $limit Nombre de résultats
     * @return array
     */
    public static function getTopMenus(int $limit = 10): array
    {
        if (!self::connect()) return [];
        
        try {
            $pipeline = [
                [
                    '$group' => [
                        '_id' => '$menu_id',
                        'titre' => ['$first' => '$menu_titre'],
                        'total_vues' => ['$sum' => 1],
                        'vues_connectes' => [
                            '$sum' => ['$cond' => [['$ne' => ['$user_id', null]], 1, 0]]
                        ],
                        'vues_visiteurs' => [
                            '$sum' => ['$cond' => [['$eq' => ['$user_id', null]], 1, 0]]
                        ]
                    ]
                ],
                ['$sort' => ['total_vues' => -1]],
                ['$limit' => $limit]
            ];
            
            return iterator_to_array(self::$db->menu_views->aggregate($pipeline));
        } catch (Exception $e) {
            error_log("MongoDB getTopMenus error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les statistiques des commandes par jour
     * 
     * @param int $jours Nombre de jours
     * @return array
     */
    public static function getCommandesParJour(int $jours = 30): array
    {
        if (!self::connect()) return [];
        
        try {
            $dateDebut = date('Y-m-d', strtotime("-$jours days"));
            
            $pipeline = [
                ['$match' => ['date' => ['$gte' => $dateDebut]]],
                [
                    '$group' => [
                        '_id' => '$date',
                        'nombre_commandes' => ['$sum' => 1],
                        'total_personnes' => ['$sum' => '$nombre_personnes'],
                        'chiffre_affaires' => ['$sum' => '$montant_total']
                    ]
                ],
                ['$sort' => ['_id' => 1]]
            ];
            
            return iterator_to_array(self::$db->commande_stats->aggregate($pipeline));
        } catch (Exception $e) {
            error_log("MongoDB getCommandesParJour error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère les statistiques globales
     * 
     * @return array
     */
    public static function getStatsGlobales(): array
    {
        if (!self::connect()) return [];
        
        try {
            return [
                'total_vues_menus' => self::$db->menu_views->countDocuments(),
                'total_commandes' => self::$db->commande_stats->countDocuments(),
                'total_avis' => self::$db->avis_analytics->countDocuments(),
                'total_activites' => self::$db->user_activity->countDocuments()
            ];
        } catch (Exception $e) {
            error_log("MongoDB getStatsGlobales error: " . $e->getMessage());
            return [];
        }
    }
}
