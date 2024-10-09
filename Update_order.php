<?php
session_start();
require_once 'database.php';
require_once 'Driver_class.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $driver = new Driver($db);
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    if ($driver->updateOrderStatus($order_id, $status)) {
        // Return success response
        echo json_encode([
            'success' => true,
            'new_status' => $status
        ]);
    } else {
        // Return error response
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
}
?>
