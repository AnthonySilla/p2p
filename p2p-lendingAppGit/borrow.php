<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrowerId = $_SESSION['user_id'];
    $loanId = $_POST['loan_id'];

    $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ? AND borrower_id IS NULL AND status = 'open'");
    $stmt->execute([$loanId]);
    $loan = $stmt->fetch();

    if (!$loan) {
        $message = "⚠️ This loan is not available.";
    } else {
        $totalRepayable = $loan['amount'] + ($loan['amount'] * $loan['interest'] / 100);
        $stmt = $pdo->prepare("UPDATE loans SET borrower_id = ?, status = 'active', amount = ? WHERE id = ?");
        $stmt->execute([$borrowerId, $totalRepayable, $loanId]);

        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$borrowerId]);
        $user = $stmt->fetch();

        $newBalance = $user['balance'] + $loan['amount'];
        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$newBalance, $borrowerId]);

        $message = "✅ Loan accepted! You received {$loan['amount']} coins. Total to repay: {$totalRepayable} coins.";

        $notificationMessage = "✅ You have successfully borrowed {$loan['amount']} coins. The total repayable amount is {$totalRepayable} coins.";
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$borrowerId, $notificationMessage]);
    }
}

$stmt = $pdo->query("SELECT * FROM loans WHERE borrower_id IS NULL AND status = 'open'");
$loans = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrow Coins</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #333;
        }

        .message {
            background-color: #e9ffe9;
            padding: 12px;
            border: 1px solid #b2ffb2;
            margin-bottom: 20px;
            border-radius: 6px;
            text-align: center;
            color: #2c662d;
            font-size: 16px;
        }

        .loan-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 20px;
            transition: box-shadow 0.2s ease;
        }

        .loan-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .loan-card p {
            margin: 6px 0;
            font-size: 15px;
            color: #555;
        }

        .loan-card strong {
            color: #222;
        }

        .loan-card button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 12px;
        }

        .loan-card button:hover {
            background-color: #218838;
        }

        .back-btn {
            display: block;
            margin: 30px auto 0;
            width: 100%;
            max-width: 300px;
            padding: 12px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 480px) {
            body {
                padding: 15px;
            }

            h2 {
                font-size: 20px;
            }

            .loan-card {
                padding: 16px;
            }

            .loan-card p {
                font-size: 14px;
            }

            .loan-card button {
                font-size: 15px;
            }

            .back-btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<h2>💰 Available Loans to Borrow</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (count($loans) === 0): ?>
    <p style="text-align:center;">No available loans at the moment. Please check back later.</p>
<?php endif; ?>

<?php foreach ($loans as $loan): ?>
    <div class="loan-card">
        <form method="POST">
            <p><strong>Loan #<?= $loan['id'] ?></strong></p>
            <p><strong>Amount:</strong> <?= $loan['amount'] ?> coins</p>
            <p><strong>Interest:</strong> <?= $loan['interest'] ?>%</p>
            <p><strong>Duration:</strong> <?= $loan['duration'] ?> days</p>
            <button type="submit" name="loan_id" value="<?= $loan['id'] ?>">Accept Loan</button>
        </form>
    </div>
<?php endforeach; ?>

<a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>

</body>
</html>
