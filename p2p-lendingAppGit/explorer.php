<?php
// explorer.php
include('includes/db.php');
session_start();

// Set the number of blocks to display per page
$blocksPerPage = 10;

// Get the current page number from the URL (default is page 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the query (how many blocks to skip)
$offset = ($page - 1) * $blocksPerPage;

// Fetch the total number of blocks (for pagination calculation)
$stmt = $pdo->query("SELECT COUNT(*) FROM blocks");
$totalBlocks = $stmt->fetchColumn();
$totalPages = ceil($totalBlocks / $blocksPerPage);

// Fetch recent blocks for the current page
$stmt = $pdo->prepare("SELECT * FROM blocks ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $blocksPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$blocks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Explorer</title>
    <!-- Using Bootstrap for a modern view -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            padding: 20px;
        }

        .block {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 20px;
            border-radius: 5px;
            background: #fff;
        }
        .block h3 {
            margin-top: 0;
        }
        .pagination {
            margin-top: 20px;
            justify-content: center;
        }
        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        .pagination .page-link {
            color: #007bff;
        }

        /* Back Button */
        .back-btn {
            margin-top: 20px;
        }

        /* Mobile-friendly styles (for screens <= 480px) */
        @media screen and (max-width: 480px) {
            .container {
                padding: 10px;
            }

            .block {
                padding: 15px;
                font-size: 8px;
            }

            .block h3 {
                font-size: 18px;
            }

            .pagination .page-link {
                padding: 8px 12px;
                font-size: 14px;
            }

            .pagination .page-item {
                font-size: 12px;
            }

            .back-btn {
                width: 100%;
                padding: 12px;
                text-align: center;
                font-size: 16px;
                display: block;
            }

            .pagination {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">📦 Live Block Explorer</h2>

    <?php foreach ($blocks as $block): ?>
        <div class="block">
            <h3>Block #<?= $block['id'] ?></h3>
            <p><strong>Hash:</strong> <?= $block['hash'] ?></p>
            <p><strong>Prev Hash:</strong> <?= $block['prev_hash'] ?></p>
            <p><strong>Data:</strong> <?= htmlspecialchars($block['data']) ?></p>
            <p><strong>Nonce:</strong> <?= $block['nonce'] ?></p>
            <p><strong>Timestamp:</strong> <?= $block['timestamp'] ?></p>
        </div>
    <?php endforeach; ?>

    <!-- Pagination Links -->
    <div class="pagination">
        <nav>
            <ul class="pagination">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=1">First</a>
                </li>
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
                <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $totalPages ?>">Last</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-secondary back-btn">⬅️ Back to Dashboard</a>
</div>

<!-- Bootstrap JS, optional -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
