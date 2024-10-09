<?php
session_start();
require_once 'database.php'; 
$order_status_message = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Show welcome message if not shown before
if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true;
}

$user_id = $_SESSION['user_id'];

$database = new Database();
$conn = $database->getConnection();

// Fetch user's past orders
$sql = "SELECT * FROM orders WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle query failure
if ($orders === false) {
    die("Error querying database.");
}

// Handle form submission for checking order status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST["client_name"];
    $order_id = $_POST["order_id"];

    $sql_check = "SELECT * FROM orders WHERE id = :order_id AND LOWER(client_name) = LOWER(:client_name)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':client_name', $client_name, PDO::PARAM_STR);
    $stmt_check->execute();
    $result_check = $stmt_check->fetchAll(PDO::FETCH_ASSOC);

    if ($result_check === false) {
        die("Error querying database.");
    }

    if (count($result_check) > 0) {
        $order_status_message = "<div class='order-status'><h6>Your Order Status:</h6>";
        foreach ($result_check as $order) {
            $order_status_message .= "<p>Order ID: " . htmlspecialchars($order['id']) . " - Status: " . htmlspecialchars($order['status']) . "</p>";
        }
        $order_status_message .= "</div>";
    } else {
        $order_status_message = "<p class='text-danger'>No orders found for that name and order ID combination. Please check your details.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
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
        .order-status {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Welcome!</h2>

        <!-- Welcome Modal -->
        <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="welcomeModalLabel">Welcome to Rindra Delivery Service!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="wc.jpg" alt="Welcome" class="img-fluid">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check Order Status Section -->
        <div class="card">
            <div class="card-header">
                <h5>Check Order Status</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="order_id" class="form-label">Order ID</label>
                        <input type="number" class="form-control" id="order_id" name="order_id" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Check Status</button>
                </form>

                <?php
                if ($order_status_message) {
                    echo $order_status_message;
                }
                ?>
            </div>
        </div><br><br>

        <!-- Order History Section -->
        <div class="card">
            <div class="card-header">
                <h5>Your Order History</h5>
            </div>
            <div class="card-body">
                <?php
                if (count($orders) > 0) {
                    echo "<ul class='list-group'>";
                    foreach ($orders as $order) {
                        echo "<li class='list-group-item'>";
                        echo "Order ID: " . htmlspecialchars($order['id']) . " - Status: " . htmlspecialchars($order['status']);
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No past orders found.</p>";
                }
                ?>
            </div>
        </div>

        <div class="text-center">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (<?php echo isset($_SESSION['welcome_shown']) ? 'true' : 'false'; ?>) {
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();
                <?php unset($_SESSION['welcome_shown']); ?> 
            }
        });
    </script>
</body>
</html>

<?php
$conn = null; 
?>
