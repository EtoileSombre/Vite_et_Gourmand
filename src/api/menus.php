<?php
// API endpoint for menus
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use simple file-based service for testing
require_once __DIR__ . '/../includes/MenuService_Simple.php';

$menuService = new MenuService();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $eventType = $_GET['event_type'] ?? null;
    $id = $_GET['id'] ?? null;
    
    try {
        if ($id) {
            // Get specific menu by ID
            $menu = $menuService->getMenuById($id);
            if ($menu) {
                echo json_encode(['success' => true, 'data' => $menu]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Menu not found']);
            }
        } elseif ($eventType) {
            // Get menus by event type
            $menus = $menuService->getMenusByEventType($eventType);
            echo json_encode(['success' => true, 'data' => array_values($menus)]);
        } else {
            // Get all menus
            $menus = $menuService->getAllMenus();
            echo json_encode(['success' => true, 'data' => array_values($menus)]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>