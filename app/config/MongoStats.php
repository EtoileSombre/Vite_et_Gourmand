<?php
/**
 * MongoStats - Helper pour gérer les statistiques avec MongoDB
 * Fonctions simples pour logger et récupérer des stats
 */

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

    /**
     * Vérifie si MongoDB est disponible
     */
    public function isAvailable(): bool
    {
        return $this->mongodb !== null;
    }

    /**
     * Enregistre une vue de menu
     * 
     * @param int $menuId
     * @param array $menuData Données du menu (titre, prix, etc.)
     */
    public function logMenuView(int $menuId, array $menuData = []): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        try {
            $this->collections['menu_views']->insertOne([
                'menu_id' => $menuId,
                'menu_titre' => $menuData['titre'] ?? null,
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'heure' => date('H:i:s'),
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logMenuView : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistre une activité utilisateur
     * 
     * @param string $action Type d'action (login, commande, avis, etc.)
     * @param int|null $utilisateurId
     * @param array $details Détails supplémentaires
     */
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
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d'),
                'heure' => date('H:i:s'),
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logUserActivity : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistre une stat de commande
     * 
     * @param string $numeroCommande
     * @param array $commandeData
     */
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
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'date' => date('Y-m-d')
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur logCommande : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les menus les plus vus
     * 
     * @param int $limit Nombre de menus à retourner
     * @param int $jours Nombre de jours à analyser (défaut: 30)
     * @return array
     */
    public function getTopMenus(int $limit = 10, int $jours = 30): array
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
            return iterator_to_array($result);

        } catch (Exception $e) {
            error_log("Erreur getTopMenus : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère le nombre de vues par jour (derniers 7 jours)
     * 
     * @return array
     */
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
            return iterator_to_array($result);

        } catch (Exception $e) {
            error_log("Erreur getViewsPerDay : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les statistiques globales
     * 
     * @return array
     */
    public function getGlobalStats(): array
    {
        if (!$this->isAvailable()) {
            return [
                'total_menu_views' => 0,
                'total_user_activities' => 0,
                'total_commandes' => 0
            ];
        }

        try {
            return [
                'total_menu_views' => $this->collections['menu_views']->countDocuments(),
                'total_user_activities' => $this->collections['user_activity']->countDocuments(),
                'total_commandes' => $this->collections['commande_stats']->countDocuments(),
                'top_menus' => $this->getTopMenus(5, 30)
            ];
        } catch (Exception $e) {
            error_log("Erreur getGlobalStats : " . $e->getMessage());
            return [
                'total_menu_views' => 0,
                'total_user_activities' => 0,
                'total_commandes' => 0
            ];
        }
    }
}
