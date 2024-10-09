<?php
session_start();
require 'Database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = ''; 
$success_message = ''; 

// Create a new Database instance and get the connection
$database = new Database();
$db = $database->getConnection(); // Using the PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "Invalid CSRF token!";
    } else {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        if (validate_user_input($username, $password)) {
            $sql = "SELECT id, password, role FROM users WHERE username = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username]); // Execute with an array

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    if ($user['role'] == 'admin') {
                        header("Location: admin_dashboard.php");
                    } elseif ($user['role'] == 'client') {
                        header("Location: client.php");
                    } elseif ($user['role'] == 'driver') {
                        header("Location: driver.php");
                    }
                    exit();
                } else {
                    $error_message = "Invalid password!";
                }
            } else {
                $error_message = "User not found!";
            }
        } else {
            $error_message = "Please enter both username and password.";
        }
    }
}

// No need to close the connection when using PDO, it will automatically close at the end of the script

function validate_user_input($username, $password) {
    return !empty($username) && !empty($password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border border-primary">
                    <div class="card-body">
                        <div class="logo">
                            <img src="logo.png" alt="Logo"> 
                        </div>
                        <h2 class="text-center">Sign In</h2>
                        <?php if (!empty($error_message)): ?>
                            <div class="error-message">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="username" class="form-label">User Name</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="text-center-custom">
                                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            </div>
                        </form>
                        <div class="text-center-custom">
                            <p>If you don't have an account, please create one.</p>
                        </div>
                        <div class="text-center-custom">
                            <a href="create_account.php" class="btn btn-primary btn-block">Create your account</a>
                        </div>
                        <div class="text-center-custom mt-3">
                            <a href="terms_and_policy.php" class="link-secondary">Terms and Policy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
