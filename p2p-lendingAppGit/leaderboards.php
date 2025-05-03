<?php
session_start();
include('includes/db.php');

// Fetch the top 10 users based on their balance
$stmt = $pdo->prepare("SELECT u.id, u.balance, SUM(t.reward) AS total_rewards 
                       FROM users u
                       LEFT JOIN transactions t ON u.id = t.user_id
                       GROUP BY u.id
                       ORDER BY u.balance DESC
                       LIMIT 10");
$stmt->execute();
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to hash the user ID for anonymity and limit it to 8 characters
function getHashedUserId($userId) {
    return substr(hash('sha256', $userId), 0, 8);  // Hash user ID and return first 8 characters
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
            background-color: #f4f4f9;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }
        th, td {
            padding: 12px;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td {
            background-color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-btn {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }

        /* Responsive styling for small devices (480px or smaller) */
        @media screen and (max-width: 480px) {
            body {
                padding: 10px;
            }
            h2 {
                font-size: 20px;
            }
            table, th, td {
                font-size: 14px;
            }
            th, td {
                padding: 8px;
            }
            .back-btn {
                font-size: 16px;
                padding: 20px;
				width: 300px;
            }
        }
    </style>
</head>
<body>

<h2>Leaderboard</h2>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>User (Anonymous)</th>
            <th>Balance</th>
            <th>Total Rewards</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rank = 1;
        foreach ($leaderboard as $user) {
            // Hash the user ID for anonymity (first 8 characters)
            $hashedUserId = getHashedUserId($user['id']);
            echo "<tr>
                    <td>{$rank}</td>
                    <td>User {$hashedUserId}</td>
                    <td>{$user['balance']} coins</td>
                    <td>{$user['total_rewards']} coins</td>
                  </tr>";
            $rank++;
        }
        ?>
    </tbody>
</table>

<a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>

</body>
</html>
