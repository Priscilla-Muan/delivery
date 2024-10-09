<?php
class Client {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function viewOrderStatus($order_id) {
        $sql = "SELECT client_name, client_address, client_contact, status FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $order_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
