<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AntoCoin</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 3rem;
        }

        .header p {
            margin: 10px 0;
            font-size: 1.2rem;
        }

        /* Banner Section */
        .banner {
            background: url('images/bitcoin.jpg') no-repeat center center/cover;
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .banner h2 {
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* Key Features Section */
        .features {
            display: flex;
            justify-content: space-around;
            padding: 40px;
            background-color: #fff;
        }

        .feature {
            text-align: center;
            width: 25%;
            padding: 20px;
        }

        .feature h3 {
            color: #007bff;
            font-size: 1.5rem;
        }

        .feature p {
            font-size: 1rem;
            color: #555;
        }

        /* CTA Section */
        .cta {
            background-color: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .cta a {
            background-color: #28a745;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.6rem;
            color: white;
            transition: background-color 0.3s ease;
        }

        .cta a:hover {
            background-color: #218838;
        }

        /* Footer */
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }

        /* Mobile View */
        @media (max-width: 768px) {
            .features {
                flex-direction: column;
                align-items: center;
            }

            .feature {
                width: 80%;
                margin-bottom: 20px;
            }

            .banner h2 {
                font-size: 2rem;
            }
        }

    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>Welcome to AntoCoin</h1>
    <p>Your Gateway to Bitcoin-like Mining and Lending Platform</p>
</div>

<!-- Banner Section -->
<div class="banner">
    <h2>Mine AntoCoin and Lend to Earn!</h2>
</div>

<!-- Key Features Section -->
<div class="features">
    <div class="feature">
        <h3>Bitcoin-like Mining</h3>
        <p>AntoCoin uses a decentralized, efficient mining model to ensure you can earn by contributing your computing power.</p>
    </div>
    <div class="feature">
        <h3>Lending Platform</h3>
        <p>Offer loans to others and earn interest! AntoCoin lets you lend with ease and build your wealth.</p>
    </div>
    <div class="feature">
        <h3>Secure and Transparent</h3>
        <p>Your transactions and investments are secured on the blockchain, ensuring a transparent and safe experience.</p>
    </div>
</div>

<!-- Call to Action Section -->
<div class="cta">
    <h2>Get Started with AntoCoin Today</h2>
    <p>Sign up to start mining and lending your AntoCoins.</p>
    <a href="register.php">Register Now</a> | <a href="login.php">Already have an account? Login</a>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2025 AntoCoin. All rights reserved.</p>
</div>

</body>
</html>
