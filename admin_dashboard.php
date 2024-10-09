<?php
session_start();
require 'database.php'; 
$database = new Database();
$db = $database->getConnection(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Access denied!");
}

$orders = [];
$delivered_orders = [];
$search_query = '';

// Handle search for active orders
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = $_POST['search_query'] ?? '';
    
    // Prepare the search query for active orders
    $searchQuery = "SELECT o.*, u.username AS driver_username FROM orders o LEFT JOIN users u ON o.driver_id = u.id WHERE o.client_name LIKE :search_query AND o.status != 'Delivered'";
    $stmt = $db->prepare($searchQuery);
    $stmt->bindValue(':search_query', '%' . $search_query . '%'); // Using wildcards for LIKE
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all active orders
    $query = "SELECT o.*, u.username AS driver_username FROM orders o LEFT JOIN users u ON o.driver_id = u.id WHERE o.status != 'Delivered'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch delivered orders
$queryDelivered = "SELECT o.*, u.username AS driver_username FROM orders o LEFT JOIN users u ON o.driver_id = u.id WHERE o.status = 'Delivered' OR o.status = 'Archived'";
$stmtDelivered = $db->prepare($queryDelivered);
$stmtDelivered->execute();
$delivered_orders = $stmtDelivered->fetchAll(PDO::FETCH_ASSOC);

// Handle order creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_order'])) {
    $client_name = $_POST['client_name'];
    $client_address = $_POST['client_address'];
    $client_contact = $_POST['client_contact'];

    // Prepare the insert query
    $insertQuery = "INSERT INTO orders (client_name, client_address, client_contact, status, date_time) VALUES (:client_name, :client_address, :client_contact, 'Pending', NOW())";
    $stmt = $db->prepare($insertQuery);
    $stmt->bindValue(':client_name', $client_name);
    $stmt->bindValue(':client_address', $client_address);
    $stmt->bindValue(':client_contact', $client_contact);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Order created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error creating order!</div>";
    }
}

// Handle driver assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_driver'])) {
    $order_id = $_POST['order_id'];
    $driver_id = $_POST['driver_id'];

    // Prepare the update query
    $updateQuery = "UPDATE orders SET driver_id = :driver_id WHERE id = :order_id";
    $stmt = $db->prepare($updateQuery);
    $stmt->bindValue(':driver_id', $driver_id);
    $stmt->bindValue(':order_id', $order_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Driver assigned successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error assigning driver!</div>";
    }
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Prepare the update query
    $updateQuery = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $db->prepare($updateQuery);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':order_id', $order_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Order status updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating status!</div>";
    }
}

// Handle order removal (archive instead of delete)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_order'])) {
    $order_id = $_POST['order_id'];

    // Prepare the update query to set the status to "Archived"
    $archiveQuery = "UPDATE orders SET status = 'Archived' WHERE id = :order_id";
    $stmt = $db->prepare($archiveQuery);
    $stmt->bindValue(':order_id', $order_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Order archived successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error archiving order!</div>";
    }
}

// Fetch drivers
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

        .alert {
            margin-bottom: 20px;
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
</head>
<body>
<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    
    <!-- Search Form -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-auto">
            <label for="searchInput" class="visually-hidden">Search by Client Name</label>
            <input type="text" class="form-control" id="searchInput" name="search_query" placeholder="Search by Client Name" value="<?php echo htmlspecialchars($search_query); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" name="search" class="btn btn-primary mb-3">Search</button>
        </div>
    </form>

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

    <!-- Active Orders Table -->
    <h2 class="mt-5">Active Orders</h2>
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
                <th>Remove Order</th>
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
                    <td><?php echo htmlspecialchars($order['driver_username'] ?: 'Not Assigned'); ?></td>
                    <td>
                        <form method="POST">
                            <select name="driver_id" class="form-select" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($drivers as $driver): ?>
                                    <option value="<?php echo htmlspecialchars($driver['id']); ?>"><?php echo htmlspecialchars($driver['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <button type="submit" name="assign_driver" class="btn btn-success btn-sm">Assign</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST">
                            <select name="status" class="form-select" required>
                                <option value="Pending">Pending</option>
                                <option value="Pick Up">Pick Up</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <button type="submit" name="update_status" class="btn btn-warning btn-sm">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <button type="submit" name="remove_order" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Delivered Orders Table -->
    <h2 class="mt-5">Delivered Orders</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Client Name</th>
                <th>Client Address</th>
                <th>Client Contact</th>
                <th>Status</th>
                <th>Assigned Driver</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($delivered_orders as $delivered_order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($delivered_order['id']); ?></td>
                    <td><?php echo htmlspecialchars($delivered_order['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($delivered_order['client_address']); ?></td>
                    <td><?php echo htmlspecialchars($delivered_order['client_contact']); ?></td>
                    <td><?php echo htmlspecialchars($delivered_order['status']); ?></td>
                    <td><?php echo htmlspecialchars($delivered_order['driver_username']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
