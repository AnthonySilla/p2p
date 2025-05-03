<?php
session_start();
header('Content-Type: application/json');

$isMining = isset($_SESSION['is_mining']) && $_SESSION['is_mining'] == true;
echo json_encode(['isMining' => $isMining]);
?>
