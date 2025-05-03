<?php
session_start();
include('includes/db.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css"> 
</head>
<body>

<div class="container">
    <div class="user-info">
        <h2>Welcome, <?= htmlspecialchars($user['username']) ?></h2>
        <p>Your Balance: <?= $user['balance'] ?> coins</p>
    </div>

    <div class="card-grid">
        <a href="send.php" class="card">📤 Send Coins</a>
        <a href="mine.php" class="card">⛏️ Mine Coins</a>
        <a href="leaderboards.php" class="card">🏆 Leaderboards</a>
        <a href="explorer.php" class="card">🔍 Miner Explorer</a>
        <a href="pay_loan.php" class="card">💸 Pay Loans</a>
        <a href="borrow.php" class="card">📥 Request Loan</a>
        <a href="lend.php" class="card">📤 Lend Coins</a>
        <a href="notifications.php" class="card">📨 Notifications</a>
    </div>

    <a href="logout.php" class="logout-btn">🚪 Logout</a>
</div>

</body>
</html>
