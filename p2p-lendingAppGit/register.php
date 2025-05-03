<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        header('Location: login.php');
        exit();
    } else {
        echo "Registration failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-container {
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
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .login-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-link a {
            text-decoration: none;
            color: #007bff;
        }

        .login-link a:hover {
            color: #0056b3;
        }

        /* Mobile view */
        @media screen and (max-width: 480px) {
            .register-container {
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

<div class="register-container">
    <h2>Register</h2>

    <!-- Registration Form -->
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <!-- Login Link -->
    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

</body>
</html>
