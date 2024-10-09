<?php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $driver_id = $_POST['driver_id'];

    $update_sql = "UPDATE orders SET driver_id = ?, status = 'assigned' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $driver_id, $order_id);

    if ($stmt->execute()) {
        $update_driver_status_sql = "UPDATE users SET status = 'busy' WHERE id = ?";
        $stmt_driver_status = $conn->prepare($update_driver_status_sql);
        $stmt_driver_status->bind_param("i", $driver_id);
        $stmt_driver_status->execute();

        header("Location: admin_dashboard.php?success=Driver assigned successfully");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
