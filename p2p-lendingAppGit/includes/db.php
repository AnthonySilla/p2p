<?php
$host = 'localhost:3308';
$db = 'p2p_lendingv2';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
	//echo 'connected successfully...';
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
