<?php
session_start();
require 'database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = ''; 
$success_message = ''; 

$database = new Database();
$db = $database->getConnection(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "Invalid CSRF token!";
    } else if (isset($_POST['add_driver'])) {
        $driver_username = trim($_POST["driver_name"]);
        $driver_password = password_hash(trim($_POST["driver_password"]), PASSWORD_DEFAULT);
        $driver_contact = trim($_POST["driver_contact"]);

        if (validate_user_input($driver_username, $driver_password, $driver_contact)) {
            $sql = "INSERT INTO users (user_name, contact, status) VALUES (?, ?, 'available')";
            $stmt = $db->prepare($sql);

            if ($stmt->execute([$driver_username, $driver_contact])) {
                $last_id = $db->lastInsertId();
                $sql_password = "INSERT INTO driver_passwords (driver_id, password) VALUES (?, ?)";
                $stmt_password = $db->prepare($sql_password);
                $stmt_password->execute([$last_id, $driver_password]);

                $success_message = "Driver added successfully!";
            } else {
                $error_message = "Error adding driver: " . $stmt->errorInfo()[2];
            }
        } else {
            $error_message = "Please fill in all fields.";
        }
    } else if (isset($_POST['delete_driver'])) {
        $driver_id = $_POST['driver_id'];
        $sql_delete = "DELETE FROM users WHERE id = ?";
        $stmt_delete = $db->prepare($sql_delete);

        if ($stmt_delete->execute([$driver_id])) {
            $success_message = "Driver deleted successfully!";
        } else {
            $error_message = "Error deleting driver: " . $stmt_delete->errorInfo()[2];
        }
    }
}

// Fetch existing drivers (users)
try {
    $sql = "SELECT id, user_name, contact FROM users WHERE status = 'available'"; 
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching drivers: " . $e->getMessage();
    $users = []; // Initialize $users to avoid undefined variable error
}

function validate_user_input($name, $password, $contact) {
    return !empty($name) && !empty($password) && !empty($contact);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <style>
        body {
            background-image: url('img4.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: #333;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border border-primary">
                    <div class="card-body">
                        <h2 class="text-center">Add Driver</h2>
                        <?php if (!empty($error_message)): ?>
                            <div class="error-message">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="success-message">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="driver_name" class="form-label">Driver Name</label>
                                <input type="text" class="form-control" id="driver_name" name="driver_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="driver_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="driver_password" name="driver_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="driver_contact" class="form-label">Contact</label>
                                <input type="text" class="form-control" id="driver_contact" name="driver_contact" required>
                            </div>
                            <button type="submit" name="add_driver" class="btn btn-primary">Add Driver</button>
                        </form>

                        <h3 class="mt-4">Existing Drivers</h3>
                        <?php if (isset($users) && count($users) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td> 
                                            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['contact']); ?></td>
                                            <td>
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="driver_id" value="<?php echo htmlspecialchars($user['id']); ?>"> 
                                                    <button type="submit" name="delete_driver" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this driver?');">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No drivers found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
