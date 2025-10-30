<?php
/**
 * API REST pour récupérer les menus
 * Endpoint: /api/menus.php
 * Méthode: GET
 * Paramètres: ?regime=xxx&search=xxx
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db.php';

try {
    // Récupérer les filtres
    $regimeFilter = $_GET['regime'] ?? '';
    $searchTerm = $_GET['search'] ?? '';
    
    // Construire la requête SQL
    $sql = "
        SELECT 
            m.menu_id,
            m.titre,
            m.prix_par_personne,
            m.nombre_personne_minimum,
            m.description,
            m.conditions,
            m.quantite_restante,
            r.libelle as regime_libelle,
            r.regime_id,
            t.libelle as theme_libelle,
            t.theme_id,
            m.created_at,
            m.updated_at
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filtre par régime
    if (!empty($regimeFilter)) {
        $sql .= " AND r.libelle = ?";
        $params[] = $regimeFilter;
    }
    
    // Filtre par recherche
    if (!empty($searchTerm)) {
        $sql .= " AND (m.titre LIKE ? OR m.description LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    
    $sql .= " ORDER BY m.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les régimes disponibles
    $stmtRegimes = $pdo->query("SELECT regime_id, libelle FROM regime ORDER BY libelle");
    $regimes = $stmtRegimes->fetchAll(PDO::FETCH_ASSOC);
    
    // Réponse JSON
    echo json_encode([
        'success' => true,
        'count' => count($menus),
        'filters' => [
            'regime' => $regimeFilter,
            'search' => $searchTerm
        ],
        'regimes_disponibles' => $regimes,
        'menus' => $menus
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des menus',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
