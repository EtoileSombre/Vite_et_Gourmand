<?php
// API endpoint for orders
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/OrderService.php';

$orderService = new OrderService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new order
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $requiredFields = ['customer_name', 'customer_email', 'items', 'total_amount'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
            exit();
        }
    }
    
    // Validate email
    if (!filter_var($input['customer_email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit();
    }
    
    // Validate total amount
    if (!is_numeric($input['total_amount']) || $input['total_amount'] <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid total amount']);
        exit();
    }
    
    try {
        $orderId = $orderService->createOrder($input);
        if ($orderId) {
            echo json_encode([
                'success' => true, 
                'message' => 'Order created successfully',
                'order_id' => $orderId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create order']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get order by ID
    $orderId = $_GET['id'] ?? null;
    
    if (!$orderId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order ID is required']);
        exit();
    }
    
    try {
        $order = $orderService->getOrderById($orderId);
        if ($order) {
            echo json_encode(['success' => true, 'data' => $order]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update order status
    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $_GET['id'] ?? null;
    
    if (!$orderId || !isset($input['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
        exit();
    }
    
    try {
        $result = $orderService->updateOrderStatus($orderId, $input['status']);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
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