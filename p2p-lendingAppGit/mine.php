<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the user ID
$userId = $_SESSION['user_id'];

// Fetch the current mining attempts and last reset date from the database
$stmt = $pdo->prepare("SELECT mining_attempts_today, last_reset_date FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found!";
    exit();
}

$miningAttemptsToday = $user['mining_attempts_today'];
$lastResetDate = $user['last_reset_date'];

// Get the current date
$currentDate = date("Y-m-d");

// Check if the mining attempts need to be reset
if ($lastResetDate !== $currentDate) {
    // Reset mining attempts at the start of the new day
    $stmt = $pdo->prepare("UPDATE users SET mining_attempts_today = 0, last_reset_date = ? WHERE id = ?");
    $stmt->execute([$currentDate, $userId]);

    // Fetch the updated user data after resetting the attempts
    $stmt = $pdo->prepare("SELECT mining_attempts_today, last_reset_date FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $miningAttemptsToday = $user['mining_attempts_today'];
    $lastResetDate = $user['last_reset_date'];
}

// Limit: 10 attempts per day
$maxAttempts = 10;

// Check if the user has exceeded the daily limit of attempts
if ($miningAttemptsToday >= $maxAttempts) {
    // Display an alert and redirect to dashboard.php
    echo "<script>alert('❌ You have reached the maximum number of mining attempts for today.'); window.location.href='dashboard.php';</script>";
    exit();
}


// 🔢 Fetch current block height
try {
    $totalBlocks = $pdo->query("SELECT COUNT(*) FROM blocks")->fetchColumn();
    if ($totalBlocks === false) {
        throw new Exception("Error fetching block height");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// 🪙 Calculate current reward with halving algorithm
$baseReward = 10;
$halvingInterval = 200;
$halvingCount = floor($totalBlocks / $halvingInterval);
$currentReward = max(1, floor($baseReward / pow(2, $halvingCount)));

// Increment the mining attempt count for today in the database
$stmt = $pdo->prepare("UPDATE users SET mining_attempts_today = mining_attempts_today + 1 WHERE id = ?");
$stmt->execute([$userId]);

// Mining success logic (add coins to user's balance, or whatever logic you use here)

// Continue with the rest of the mining logic...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mine Coins</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <link rel="stylesheet" href="assets/css/style.css"> <!-- Ensure this file is correct -->
</head>
<body>

<div class="container">
    <h2>🛠️ Mining Interface</h2>

    <div class="info">
        <p><strong>Current Block Height:</strong> <?= $totalBlocks ?></p>
        <p><strong>Current Mining Reward:</strong> <?= $currentReward ?> coin<?= $currentReward > 1 ? 's' : '' ?></p>
    </div>

    <!-- Mining Button -->
    <button id="mineBtn">⛏️ Mine Now</button>

    <!-- Loading Indicator -->
    <div class="loading" id="loadingMsg">⏳ Mining in progress...</div>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>

<script src="assets/js/mine.js"></script>

<script>
document.getElementById('mineBtn').addEventListener('click', function () {
    // Disable the mine button and show loading message
    document.getElementById('mineBtn').disabled = true;
    document.getElementById('loadingMsg').style.display = 'block';

    // Start mining process (assuming this function is defined in mine.js)
    startMining(<?= $_SESSION['user_id'] ?>);
});

// Function to handle the mining completion and re-enable the button
function miningCompleted() {
    document.getElementById('mineBtn').disabled = false;
    document.getElementById('loadingMsg').style.display = 'none';
}
</script>

</body>
</html>
