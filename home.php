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
            background-image: url('bgpic.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: #ffffff;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            height: 100vh; /* Full height for vertical centering */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space between header and content */
            align-items: center; /* Center horizontally */
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%; 
            position: absolute; 
            top: 0; 
            left: 0; 
            z-index: 1000; 
        }
        .header img {
            max-width: 150px;
            animation: bounce 1.5s infinite alternate;
        }
        .header h1 {
            font-size: 2em;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 102, 204, 0.2);
            margin: 0; 
        }
        .content {
            text-align: center;
            margin-top: auto; 
            margin-bottom: 50px; 
        }
        .interesting-fact {
            margin: 20px 0;
            font-size: 1.2em;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 10px 15px;
            border-radius: 8px;
        }
        .sign-up-button {
            margin-top: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.8s forwards 1s;
            transition: transform 0.3s ease;
            font-size: 1.3em; 
            padding: 15px 30px; 
        }
        .sign-up-button:hover {
            transform: translateY(-5px);
            background-color: #0056b3;
        }

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
        <h1>Welcome to Rindra Delivery Service!</h1>
        <div>
            <a href="terms_and_policy.php" class="btn btn-link text-light">Terms and Policy</a>
        </div>
    </header>

    <div class="content">
        <p class="interesting-fact">Did you know? Our delivery service ensures that your packages are delivered with utmost care and speed, making your life easier and more convenient.</p>
        <a href="create_account.php" class="btn btn-primary sign-up-button">Sign Up Now</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
