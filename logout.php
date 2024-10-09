<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<style>
    body{
        background-color: #BBD1D5;
    }
    .card {
        background-color: #ffffff;
        border: 1px solid #007bff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    .btn-homepage {
        background-color: #007bff;
        color: white;
        border-radius: 5px;
    }
    .card-img-top {
        border-radius: 10px;
        width: 100%;
        height: 400px; 
    }
</style>
<body>
<div class="container mt-5">
    <div class="text-center">
        <h2 class="text-center">Thank you for supporting us!</h2>
        <p>We appreciate your business. Come visit us again soon!</p>
    </div>

    <div class="row mt-4 justify-content-center">
        <!-- Appreciation Card 1 -->
        <div class="col-md-4 mb-3">
            <div class="card">
                <img src="img2.jpg" alt="Appreciation Image 1" class="card-img-top">
                <div class="card-header text-center">You're Awesome!</div>
                <div class="card-body text-center">
                    <p>Thank you for being a valued client. Your support helps us grow!</p>
                </div>
            </div>
        </div>

        <!-- Appreciation Card 2 -->
        <div class="col-md-4 mb-3">
            <div class="card">
                <img src="img3.jpg" alt="Appreciation Image 2" class="card-img-top">
                <div class="card-header text-center">We Appreciate You!</div>
                <div class="card-body text-center">
                    <p>We hope to see you again soon. Your satisfaction is our priority!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="home.php" class="btn btn-homepage">Go to Homepage</a>
    </div>
</div>
</body>
</html>
