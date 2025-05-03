<?php
include('includes/db.php');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lenderId = $_SESSION['user_id'];
    $amount = $_POST['amount']; // The amount to lend
    $interest = $_POST['interest']; // Interest rate in percentage
    $duration = $_POST['duration']; // Duration in days

    // Fetch the lender's balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$lenderId]);
    $user = $stmt->fetch();
    if (!$user || $user['balance'] < $amount) {
        echo "Insufficient balance to lend!";
        exit();
    }

    // Reduce lender's balance
    $newBalance = $user['balance'] - $amount;
    $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->execute([$newBalance, $lenderId]);

    // Insert the loan into the `loans` table
    $stmt = $pdo->prepare("INSERT INTO loans (borrower_id, amount, interest, duration, funded_by, status) 
                           VALUES (NULL, ?, ?, ?, ?, 'open')");
    $stmt->execute([$amount, $interest, $duration, $lenderId]);

    // Create a notification for the lender
    $notificationMessage = "✅ You have successfully created a loan of {$amount} coins with an interest rate of {$interest}% for a duration of {$duration} days.";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$lenderId, $notificationMessage]);

    // Redirect to dashboard.php after successful loan creation
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Loan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            padding: 30px;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .loan-form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 500px;
            width: 100%;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #28a745;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .message {
            background-color: #e9ffe9;
            padding: 10px;
            border: 1px solid #b2ffb2;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width: 85%;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 480px) {
            .loan-form-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            label, input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="loan-form-container">
    <h2>💸 Create a Loan</h2>

    <form method="POST">
        <label for="amount">Amount to Lend:</label>
        <input type="number" name="amount" required>

        <label for="interest">Interest Rate (%):</label>
        <input type="number" name="interest" required>

        <label for="duration">Loan Duration (in days):</label>
        <input type="number" name="duration" required>

        <button type="submit">Create Loan</button>
    </form>

    <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
