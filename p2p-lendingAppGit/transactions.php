<?php
session_start();
include('includes/db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch transaction history for the logged-in user
$stmt = $pdo->prepare("SELECT t.id, t.block_id, t.reward, t.timestamp, b.prev_hash
                       FROM transactions t
                       JOIN blocks b ON t.block_id = b.id
                       WHERE t.user_id = ? 
                       ORDER BY t.timestamp DESC");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Transaction History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background: #ddd;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<h2>Your Transaction History</h2>

<table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Block ID</th>
            <th>Previous Block Hash</th>
            <th>Reward</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($transactions): ?>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['id'] ?></td>
                    <td><?= $transaction['block_id'] ?></td>
                    <td><?= $transaction['prev_hash'] ?></td>
                    <td><?= $transaction['reward'] ?> coin<?= $transaction['reward'] > 1 ? 's' : '' ?></td>
                    <td><?= $transaction['timestamp'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No transactions found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>

</body>
</html>
