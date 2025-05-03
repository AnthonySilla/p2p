<?php
include('includes/db.php'); // Make sure this path is correct

$stmt = $pdo->query("SELECT hash FROM blocks ORDER BY id DESC LIMIT 1");
$latest = $stmt->fetchColumn();
echo $latest ?: str_repeat("0", 64); // Genesis block fallback
