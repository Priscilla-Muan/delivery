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

    <!-- Display Search Results -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])): ?>
        <h2 class="mt-5">Search Results</h2>
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
                <?php if (!empty($orders)): ?>
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
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No results found for "<?php echo htmlspecialchars($search_query); ?>"</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

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
</div>
</body>
</html>
