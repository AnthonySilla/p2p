<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("❌ Not logged in");
}

$json = file_get_contents("php://input");
file_put_contents('mine_debug.log', "RAW JSON:\n$json\n", FILE_APPEND);

$data = json_decode($json, true);

$prev = $data['prevHash'] ?? '';
$nonce = (int)($data['nonce'] ?? 0);
$clientHash = $data['hash'] ?? '';
$rawData = $data['data'] ?? '';
$difficulty = '0000';

$serverText = $prev . $rawData . $nonce;
$serverHash = hash('sha256', $serverText);

file_put_contents('mine_debug.log', json_encode([
    'prevHash' => $prev,
    'data' => $rawData,
    'nonce' => $nonce,
    'clientHash' => $clientHash,
    'serverText' => $serverText,
    'serverHash' => $serverHash
], JSON_PRETTY_PRINT), FILE_APPEND);

if ($serverHash !== $clientHash) {
    http_response_code(400);
    exit("❌ Hash mismatch!\nClient: $clientHash\nServer: $serverHash");
}

if (substr($serverHash, 0, strlen($difficulty)) !== $difficulty) {
    http_response_code(400);
    exit("❌ Hash does not meet difficulty: $difficulty");
}

try {
    // Insert mined block into DB
    $stmt = $pdo->prepare("INSERT INTO blocks (prev_hash, data, nonce, hash, timestamp) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$prev, $rawData, $nonce, $clientHash]);

    // Get the ID of the last inserted block (this will be used in the transactions table)
    $blockId = $pdo->lastInsertId();

    // ⛏️ Halving logic: reward halves every 200 blocks
    $baseReward = 10;
    $halvingInterval = 200;

    $totalBlocks = $pdo->query("SELECT COUNT(*) FROM blocks")->fetchColumn();
    $halvingCount = floor($totalBlocks / $halvingInterval);

    // Calculate current reward with halving
    $reward = max(1, floor($baseReward / pow(2, $halvingCount)));

    // Update user balance
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$reward, $_SESSION['user_id']]);

    // Log the transaction for tracking
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, block_id, reward) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $blockId, $reward]);
	
	// Create a notification for the user
	$notificationMessage = "🎉 Congratulations! You mined a block and earned $reward coins!";
	$stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
	$stmt->execute([$_SESSION['user_id'], $notificationMessage]);

    echo "✅ Block mined successfully! You earned $reward coins.";
} catch (PDOException $e) {
    // Catching any PDO errors and logging them for debugging
    file_put_contents('mine_debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    exit("❌ Error occurred: " . $e->getMessage());
}
?>
