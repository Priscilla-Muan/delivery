<?php
session_start();
require 'Database.php';
require 'Driver_class.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'driver') {
    die("Access denied!");
}

$driver = new Driver($db);
$assigned_orders = $driver->getAssignedOrders($_SESSION['user_id']);

$updated_order = null; // Variable to hold updated order details
$success_message = ''; // Variable to hold success message

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status']; 

    $validStatuses = ['Pending', 'Pick Up', 'Delivered'];
    if (!in_array($status, $validStatuses)) {
        echo '<div class="alert alert-danger" role="alert">Invalid status.</div>';
    } else {
        try {
            if ($driver->updateOrderStatus($order_id, $status)) {
                $success_message = "Order status updated successfully.";
                // Refresh assigned orders after update
                $assigned_orders = $driver->getAssignedOrders($_SESSION['user_id']);
                // Get the specific updated order
                $stmt = $db->prepare("SELECT * FROM orders WHERE id = :order_id");
                $stmt->bindParam(':order_id', $order_id);
                $stmt->execute();
                $updated_order = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('img6.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: #333;
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.8); 
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff; 
        }

        h2 {
            margin-top: 30px;
            color: #0056b3; 
        }

        .table {
            background-color: #ffffff; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .btn-warning {
            background-color: #ffc107; 
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800; 
        }

        form {
            display: inline-block; 
        }

        select {
            border-radius: 5px;
            padding: 5px;
            margin-right: 5px;
            border: 1px solid #ced4da; 
        }

        .updated-order-table {
            margin-top: 30px;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Driver Dashboard</h1>

    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <h2>Assigned Orders</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $assigned_orders->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] !== 'Delivered'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <select name="status" required>
                                    <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Pick Up" <?php echo $row['status'] == 'Pick Up' ? 'selected' : ''; ?>>Pick Up</option>
                                    <option value="Delivered" <?php echo $row['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-warning">Update Status</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Status updated to Delivered</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($updated_order): ?>
    <h2>Updated Order</h2>
    <table class="table updated-order-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($updated_order['id']); ?></td>
                <td><?php echo htmlspecialchars($updated_order['client_name']); ?></td>
                <td><?php echo htmlspecialchars($updated_order['status']); ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
