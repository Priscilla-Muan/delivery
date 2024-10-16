<?php
require 'database.php'; 

session_start();

$message = ''; 
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $contact = isset($_POST["contact"]) ? $_POST["contact"] : null; 

    if (validate_user_input($username, $password, $email, $role, $contact)) {
        $sanitized_username = $conn->quote($username); 
        $sanitized_password = password_hash($password, PASSWORD_DEFAULT);
        $sanitized_email = $conn->quote($email); 
        // Check if contact is not empty before quoting it
        $sanitized_contact = !empty($contact) ? $conn->quote($contact) : "NULL"; 
        $check_sql = "SELECT * FROM users WHERE username=$sanitized_username";
        $check_result = $conn->query($check_sql);

        if ($check_result->rowCount() > 0) {
            $message = '<div class="alert alert-danger text-center">This account already exists. 
            <a href="index.php" class="alert-link">Go to homepage</a> to log in.</div>';
        } else {
            // Ensure to use NULL directly for NULL values
            $sql = "INSERT INTO users (username, password, email, role, contact) 
                    VALUES ($sanitized_username, '$sanitized_password', $sanitized_email, '$role', $sanitized_contact)";

            try {
                if ($conn->exec($sql)) {
                    $message = '<div class="alert alert-success text-center">Account created successfully. You can now log in.</div>';
                } else {
                    $message = '<div class="alert alert-danger text-center">Error: ' . $conn->errorInfo()[2] . '</div>'; 
                }
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger text-center">Error: ' . $e->getMessage() . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger text-center">Invalid user input.</div>';
    }
}

function validate_user_input($username, $password, $email, $role, $contact) {
    $is_valid = !empty($username) && !empty($password) && !empty($email) &&
                filter_var($email, FILTER_VALIDATE_EMAIL) &&
                in_array($role, array("admin", "client", "driver"));

    if ($role === "driver") {
        return $is_valid && !empty($contact); 
    }
    
    return $is_valid;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Rindra Delivery Service - Create Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <style>
        body {
            background-image: url('img1.jpg'); 
            background-size: cover;
            background-position: center;
            color: #333;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #007bff; 
            border-radius: 10px;
        }
        .alert {
            border-radius: 5px;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .hidden-field {
            display: none;
        }
    </style>
    <script>
        function toggleContactField() {
            var role = document.getElementById("role").value;
            var contactField = document.getElementById("contactField");
            if (role === "driver") {
                contactField.style.display = "block";
            } else {
                contactField.style.display = "none";
            }
        }
    </script>
</head>

<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 border border-primary">
                <h2 class="text-center">Create Your Account</h2>
                <?php if (!empty($message)): ?>
                    <?php echo $message; ?>
                <?php endif; ?>

                <form action="create_account.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-control" onchange="toggleContactField()" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="client">Client</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>

                    <!-- Hidden contact field for drivers -->
                    <div class="mb-3 hidden-field" id="contactField">
                        <label for="contact" class="form-label">Driver Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" maxlength="55">
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form><br>

                <div class="links">
                    <a href="index.php">Sign In</a>
                    <a href="terms_and_policy.php">Terms and Policy</a>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
