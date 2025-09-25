<?php
// Menu service to handle MongoDB operations
require_once __DIR__ . '/../config/database.php';

class MenuService {
    private $mongodb;
    private $database;

    public function __construct() {
        $this->mongodb = DatabaseConfig::getMongoDBConnection();
        $this->database = DatabaseConfig::$mongodb['database'];
    }

    public function getAllMenus() {
        try {
            $query = new MongoDB\Driver\Query(['active' => true]);
            $cursor = $this->mongodb->executeQuery($this->database . '.menus', $query);
            $menus = $cursor->toArray();
            
            // Convert ObjectId to string for JSON serialization
            foreach ($menus as &$menu) {
                $menu->_id = (string)$menu->_id;
            }
            
            return $menus;
        } catch (Exception $e) {
            error_log("Error fetching menus: " . $e->getMessage());
            return [];
        }
    }

    public function getMenusByEventType($eventType) {
        try {
            $query = new MongoDB\Driver\Query([
                'active' => true,
                'event_type' => $eventType
            ]);
            $cursor = $this->mongodb->executeQuery($this->database . '.menus', $query);
            $menus = $cursor->toArray();
            
            // Convert ObjectId to string for JSON serialization
            foreach ($menus as &$menu) {
                $menu->_id = (string)$menu->_id;
            }
            
            return $menus;
        } catch (Exception $e) {
            error_log("Error fetching menus by event type: " . $e->getMessage());
            return [];
        }
    }

    public function getMenuById($id) {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);
            $query = new MongoDB\Driver\Query([
                '_id' => $objectId,
                'active' => true
            ]);
            $cursor = $this->mongodb->executeQuery($this->database . '.menus', $query);
            $menus = $cursor->toArray();
            
            if (!empty($menus)) {
                $menu = $menus[0];
                $menu->_id = (string)$menu->_id;
                return $menu;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error fetching menu by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getEventTypes() {
        try {
            $query = new MongoDB\Driver\Query(['active' => true]);
            $cursor = $this->mongodb->executeQuery($this->database . '.menus', $query);
            $menus = $cursor->toArray();
            
            $eventTypes = [];
            foreach ($menus as $menu) {
                if (!in_array($menu->event_type, $eventTypes)) {
                    $eventTypes[] = $menu->event_type;
                }
            }
            
            return array_unique($eventTypes);
        } catch (Exception $e) {
            error_log("Error fetching event types: " . $e->getMessage());
            return [];
        }
    }
}
?>