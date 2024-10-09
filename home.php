<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Rindra Delivery Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <style>
        body {
            background-image: url('bgPic.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: #333;
            overflow: hidden;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            opacity: 0;
            transform: translateY(-50px);
            animation: fadeInDown 0.8s forwards;
        }
        .header img {
            max-width: 150px;
            animation: bounce 1.5s infinite alternate;
        }
        .content {
            text-align: center;
            margin-top: 100px;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeInUp 0.8s forwards 0.5s;
        }
        .interesting-fact {
            margin: 20px 0;
            font-size: 1.2em;
        }
        .sign-up-button {
            margin-top: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.8s forwards 1s;
            transition: transform 0.3s ease;
        }
        .sign-up-button:hover {
            transform: translateY(-5px);
        }

        /* Keyframe animations */
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        @keyframes fadeInDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes bounce {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div>
            <img src="logo.png" alt="Rindra Delivery Service Logo">
        </div>
        <div>
            <a href="terms_and_policy.php" class="btn btn-link">Terms and Policy</a>
        </div>
    </header>

    <div class="content">
        <h1>Welcome to Rindra Delivery Service!</h1>
        <p class="interesting-fact">Did you know? Our delivery service ensures that your packages are delivered with utmost care and speed, making your life easier and more convenient.</p>
        <a href="create_account.php" class="btn btn-primary sign-up-button">Sign Up Now</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
