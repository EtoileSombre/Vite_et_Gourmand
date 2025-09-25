<?php
// API endpoint for event types
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/MenuService.php';

$menuService = new MenuService();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $eventTypes = $menuService->getEventTypes();
        echo json_encode(['success' => true, 'data' => $eventTypes]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>