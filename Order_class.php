<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAssignedOrders($driver_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE driver_id = :driver_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driver_id', $driver_id);
        $stmt->execute();
        return $stmt; 
    }
}
?>
