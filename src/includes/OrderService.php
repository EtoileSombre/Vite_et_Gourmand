<?php
// Order service to handle MySQL operations
require_once __DIR__ . '/../config/database.php';

class OrderService {
    private $pdo;

    public function __construct() {
        $this->pdo = DatabaseConfig::getMySQLConnection();
    }

    public function createOrder($orderData) {
        try {
            $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, items, total_amount, delivery_date, notes) 
                    VALUES (:customer_name, :customer_email, :customer_phone, :customer_address, :items, :total_amount, :delivery_date, :notes)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':customer_name' => $orderData['customer_name'],
                ':customer_email' => $orderData['customer_email'],
                ':customer_phone' => $orderData['customer_phone'] ?? null,
                ':customer_address' => $orderData['customer_address'] ?? null,
                ':items' => json_encode($orderData['items']),
                ':total_amount' => $orderData['total_amount'],
                ':delivery_date' => $orderData['delivery_date'] ?? null,
                ':notes' => $orderData['notes'] ?? null
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }

    public function getOrderById($orderId) {
        try {
            $sql = "SELECT * FROM orders WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $orderId]);
            
            $order = $stmt->fetch();
            if ($order) {
                $order['items'] = json_decode($order['items'], true);
            }
            
            return $order;
        } catch (PDOException $e) {
            error_log("Error fetching order: " . $e->getMessage());
            return null;
        }
    }

    public function updateOrderStatus($orderId, $status) {
        try {
            $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                throw new InvalidArgumentException("Invalid order status");
            }

            $sql = "UPDATE orders SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':status' => $status,
                ':id' => $orderId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }

    public function getEventTypes() {
        try {
            $sql = "SELECT * FROM event_types WHERE active = 1 ORDER BY name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching event types: " . $e->getMessage());
            return [];
        }
    }
}
?>