<?php
require_once 'User_class.php';
require_once 'Order_class.php'; 

class Driver extends User {
    public function __construct($db) {
        parent::__construct($db); 
    }

    public function getAssignedOrders($driver_id) {
        $order = new Order($this->conn); 
        return $order->getAssignedOrders($driver_id);
    }

    public function updateOrderStatus($order_id, $status) {
        $validStatuses = ['Pending', 'Pick Up', 'Delivered'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Invalid status value: $status");
        }
        $query = "UPDATE orders SET status = :status WHERE id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':order_id', $order_id);

        if ($stmt->execute()) {
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error executing query: " . implode(", ", $errorInfo));
        }
    }
}
?>
