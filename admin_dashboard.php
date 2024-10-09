<?php
session_start();
require 'database.php'; // Database connection class

$database = new Database();
$db = $database->getConnection(); // Get database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access denied!"); // Ensure the user is logged in as an admin
}

// Initialize variables
$orders = [];
$drivers = [];

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create_order'])) {
        // Handle create order
        $client_name = $_POST['client_name'];
        $client_address = $_POST['client_address'];
        $client_contact = $_POST['client_contact'];

        // Create Order
        $createQuery = "INSERT INTO orders (client_name, client_address, client_contact, status, date_time) VALUES (:client_name, :client_address, :client_contact, 'Pending', NOW())";
        $stmt = $db->prepare($createQuery);
        $stmt->bindParam(':client_name', $client_name);
        $stmt->bindParam(':client_address', $client_address);
        $stmt->bindParam(':client_contact', $client_contact);
        $stmt->execute();
    } elseif (isset($_POST['update_status'])) {
        // Handle update status
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        // Validate the status value before updating
        $valid_statuses = ['Pending', 'On the way', 'Delivered'];
        if (!in_array($status, $valid_statuses)) {
            echo "Invalid status value!";
        } else {
            // Update Order Status
            $updateQuery = "UPDATE orders SET status = :status WHERE id = :order_id";
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':order_id', $order_id);
            if ($stmt->execute()) {
                echo "Order status updated successfully.";
            } else {
                echo "Error updating order status.";
            }
        }
    } elseif (isset($_POST['delete_order'])) {
        // Handle delete order
        $order_id = $_POST['order_id'];

        // Delete Order
        $deleteQuery = "DELETE FROM orders WHERE id = :order_id";
        $stmt = $db->prepare($deleteQuery);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
    } elseif (isset($_POST['assign_driver'])) {
        // Handle assigning a driver to an order
        $order_id = $_POST['order_id'];
        $driver_id = $_POST['driver_id'];

        // Assign Driver
        $assignQuery = "UPDATE orders SET driver_id = :driver_id WHERE id = :order_id";
        $stmt = $db->prepare($assignQuery);
        $stmt->bindParam(':driver_id', $driver_id);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
    }
}

// Fetch all orders
$query = "SELECT o.*, u.username AS driver_username FROM orders o LEFT JOIN users u ON o.driver_id = u.id";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all drivers
$queryDrivers = "SELECT id, username FROM users WHERE role = 'driver'";
$stmtDrivers = $db->prepare($queryDrivers);
$stmtDrivers->execute();
$drivers = $stmtDrivers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
    <style>
        body {
            background-image: url('img6.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
                    color: #333; 
        }

        .table {
            background-color: rgba(255, 255, 255, 0.85); 
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 185px;
        }

        .text-center-custom {
            text-align: center;
            margin: 10px 0;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .table thead th {
            background-color: #6c757d; 
            color: white;
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

    </style>
<body>
<div class="container mt-5">
    <h1>Admin Dashboard</h1>

    <!-- Create Order Form -->
    <h2>Create Order</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="client_name" class="form-label">Client Name</label>
            <input type="text" class="form-control" id="client_name" name="client_name" required>
        </div>
        <div class="mb-3">
            <label for="client_address" class="form-label">Client Address</label>
            <input type="text" class="form-control" id="client_address" name="client_address" required>
        </div>
        <div class="mb-3">
            <label for="client_contact" class="form-label">Client Contact</label>
            <input type="text" class="form-control" id="client_contact" name="client_contact" required>
        </div>
        <button type="submit" name="create_order" class="btn btn-primary">Create Order</button>
    </form>

    <!-- Orders Table -->
    <h2 class="mt-5">All Orders</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Client Name</th>
                <th>Client Address</th>
                <th>Client Contact</th>
                <th>Status</th>
                <th>Assigned Driver</th>
                <th>Assign Driver</th>
                <th>Update Status</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['client_address']); ?></td>
                    <td><?php echo htmlspecialchars($order['client_contact']); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo htmlspecialchars($order['driver_username'] ?: 'Not assigned'); ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="driver_id" class="form-select" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($drivers as $driver): ?>
                                    <option value="<?php echo $driver['id']; ?>"><?php echo htmlspecialchars($driver['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_driver" class="btn btn-warning btn-sm mt-1">Assign</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="form-select" required>
                                <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="On the way" <?php echo $order['status'] == 'On the way' ? 'selected' : ''; ?>>On the way</option>
                                <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-success btn-sm mt-1">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm mt-1">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Order History Section -->
    <h2 class="mt-5">Order History</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Client Name</th>
                <th>Status</th>
                <th>Date/Time</th>
                <th>Assigned Driver</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php if ($order['status'] == 'Delivered'): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['date_time']); ?></td>
                        <td><?php echo htmlspecialchars($order['driver_username'] ?: 'Not assigned'); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
