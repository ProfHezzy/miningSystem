<?php
session_start();
$_SESSION['visited_index'] = true; 
$_SESSION['LAST_ACTIVITY'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mining Platform - Earn Points Daily</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            text-align: center;
        }
        header {
            padding: 50px 20px;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            background: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: 0.3s;
        }
        .btn:hover {
            background: #e67e22;
        }
        .features {
            padding: 50px 20px;
        }
        .feature-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }
        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }
        .feature i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #f39c12;
        }
        footer {
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            position: relative;
            bottom: 0;
            width: 100%;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            .feature-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to CryptoMine</h1>
        <p>Mine points daily and redeem amazing rewards!</p>
        <div class="buttons">
            <a href="register.html" class="btn">Register</a>
            <a href="login.html" class="btn">Login</a>
        </div>
    </header>

    <section class="features">
        <h2>Why Choose CryptoMine?</h2>
        <div class="feature-container">
            <div class="feature">
                <i class="fas fa-coins"></i>
                <h3>Daily Mining</h3>
                <p>Earn points every 24 hours by simply clicking the mine button.</p>
            </div>
            <div class="feature">
                <i class="fas fa-user-shield"></i>
                <h3>Secure & Reliable</h3>
                <p>We ensure top security measures for your account and earnings.</p>
            </div>
            <div class="feature">
                <i class="fas fa-gift"></i>
                <h3>Redeem Rewards</h3>
                <p>Convert your points into real benefits like cash and gifts.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 CryptoMine. All Rights Reserved.</p>
    </footer>
</body>
</html>
