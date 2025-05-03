<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare and execute query to check user credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    // Check if user exists and password is correct
    if ($user && password_verify($_POST['password'], $user['password'])) {
        // Store user ID in session
        $_SESSION['user_id'] = $user['id'];

        // Get the current date
        $currentDate = date("Y-m-d");

        // Check if the session already has the last reset date
        if (!isset($_SESSION['last_reset_date']) || $_SESSION['last_reset_date'] !== $currentDate) {
            // Reset mining attempts if it's a new day
            $_SESSION['mining_attempts_today'] = 0;
            $_SESSION['last_reset_date'] = $currentDate; // Update last reset date
        }

        // Redirect to the dashboard after successful login
        header('Location: dashboard.php');
        exit();
    } else {
        // If login fails, show an error message
        echo "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .register-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            text-decoration: none;
            color: #007bff;
        }

        .register-link a:hover {
            color: #0056b3;
        }

        /* Mobile view */
        @media screen and (max-width: 480px) {
            .login-container {
                padding: 20px;
                width: 90%;
            }

            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <!-- Login Form -->
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <!-- Register Link -->
    <div class="register-link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>

</body>
</html>
