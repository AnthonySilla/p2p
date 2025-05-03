<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';

// Fetch users for dropdown
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$userId]);
$users = $stmt->fetchAll();

// Handle send
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['receiver_id'];
    $amount = floatval($_POST['amount']);
    $note = trim($_POST['note']);

    if ($amount <= 0) {
        $message = "❌ Invalid amount!";
    } else {
        $stmt = $pdo->prepare("SELECT balance, username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $sender = $stmt->fetch();

        if (!$sender) {
            $message = "❌ Sender not found!";
        } elseif ($sender['balance'] < $amount) {
            $message = "❌ Not enough balance!";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);

            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $receiverId]);

            $stmt = $pdo->prepare("INSERT INTO send_transactions (sender_id, receiver_id, amount, note) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $receiverId, $amount, $note]);

            $receiverUsername = '';
            foreach ($users as $u) {
                if ($u['id'] == $receiverId) {
                    $receiverUsername = $u['username'];
                    break;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$receiverId, "You received {$amount} coins from {$sender['username']}. Note: {$note}"]);

            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$userId, "You sent {$amount} coins to {$receiverUsername}. Note: {$note}"]);

            $message = "✅ Sent {$amount} coins!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Coins</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        select, input[type="number"], textarea {
            width: 100%;
            padding: 12px;
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
            padding: 14px;
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
            color: #2c662d;
        }

        .back-btn {
            display: block;
            margin-top: 20px;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 480px) {
            .form {
                padding: 20px;
                border-radius: 0;
                box-shadow: none;
            }

            h2 {
                font-size: 20px;
            }

            label, select, input, textarea, button {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="form">
    <h2>🚀 Send Coins</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="receiver_id">Recipient:</label>
        <select name="receiver_id" required>
            <option value="">-- Select user --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" step="0.01" min="0.01" required>

        <label for="note">Note (optional):</label>
        <textarea name="note" placeholder="e.g. Payment for lunch"></textarea>

        <button type="submit">Send Coins</button>
    </form>

    <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
