<?php
session_start();
require 'Database.php';
require 'Order.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access denied!");
}

$order_id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_id = $_POST['driver_id'];
    $status = $_POST['status'];

    $order = new Order($db);
    if ($order->updateStatus($order_id, $driver_id, $status)) {
        $success_message = "Order updated successfully!";
    } else {
        $error_message = "Failed to update order.";
    }
}

$order_details = new Order($db);
$order_data = $order_details->getAssignedOrders($order_id)->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container mt-5">
    <h1>Update Order</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="driver_id" class="form-label">Driver ID</label>
            <input type="text" class="form-control" id="driver_id" name="driver_id" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="picked up">Picked Up</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Order</button>
    </form>
</div>
</body>
</html>
