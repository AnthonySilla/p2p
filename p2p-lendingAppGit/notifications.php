<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$lenderId = $_SESSION['user_id']; // Lender ID

// Number of notifications per page
$notificationsPerPage = 5;

// Get the current page number from the URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $notificationsPerPage;

// Fetch notifications for the lender with pagination
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':user_id', $lenderId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $notificationsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$notifications = $stmt->fetchAll();

// Get total number of notifications for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id");
$stmt->bindValue(':user_id', $lenderId, PDO::PARAM_INT);
$stmt->execute();
$totalNotifications = $stmt->fetchColumn();

// Calculate total number of pages
$totalPages = ceil($totalNotifications / $notificationsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Ensure the path to the CSS file is correct -->
</head>
<body>

<div class="container">
    <h3>Notifications</h3>

    <div class="notifications-container">
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-card">
                <div class="notification-header">
                    <strong><?= date('Y-m-d H:i:s', strtotime($notification['created_at'])) ?></strong>
                </div>
                <div class="notification-body">
                    <?= htmlspecialchars($notification['message']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="notifications.php?page=1" class="pagination-link">First</a>
            <a href="notifications.php?page=<?= $page - 1 ?>" class="pagination-link">Previous</a>
        <?php endif; ?>

        <span>Page <?= $page ?> of <?= $totalPages ?></span>

        <?php if ($page < $totalPages): ?>
            <a href="notifications.php?page=<?= $page + 1 ?>" class="pagination-link">Next</a>
            <a href="notifications.php?page=<?= $totalPages ?>" class="pagination-link">Last</a>
        <?php endif; ?>
    </div>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>

</div>

</body>
</html>
