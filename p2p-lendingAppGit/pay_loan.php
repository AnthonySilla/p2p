<?php
include('includes/db.php');
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';

// Process payment form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['loan_id']) || !isset($_POST['payment_amount'])) {
        $message = "⚠️ Missing loan ID or payment amount!";
    } else {
        $loanId = $_POST['loan_id'];
        $paymentAmount = floatval($_POST['payment_amount']);

        // Fetch loan
        $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ? AND borrower_id = ?");
        $stmt->execute([$loanId, $userId]);
        $loan = $stmt->fetch();

        if (!$loan) {
            $message = "❌ Loan not found!";
        } elseif ($paymentAmount <= 0) {
            $message = "❌ Invalid payment amount!";
        } elseif ($paymentAmount > $loan['amount']) {
            $message = "⚠️ You cannot pay more than the loan amount!";
        } else {
            // Deduct from borrower
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $borrower = $stmt->fetch();

            if ($borrower['balance'] < $paymentAmount) {
                $message = "❌ Insufficient balance!";
            } else {
                // Insert payment
                $stmt = $pdo->prepare("INSERT INTO payments (loan_id, amount) VALUES (?, ?)");
                $stmt->execute([$loanId, $paymentAmount]);

                // Update loan balance
                $remaining = $loan['amount'] - $paymentAmount;

                if ($remaining <= 0) {
                    // Fully paid, mark closed
                    $stmt = $pdo->prepare("UPDATE loans SET amount = 0, status = 'paid' WHERE id = ?");
                    $stmt->execute([$loanId]);

                    // Create a notification for the borrower
                    $notificationMessage = "✅ Your loan #{$loanId} has been fully repaid. Total amount paid: {$loan['amount']} coins.";
                    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt->execute([$userId, $notificationMessage]);

                } else {
                    // Partial payment
                    $stmt = $pdo->prepare("UPDATE loans SET amount = ? WHERE id = ?");
                    $stmt->execute([$remaining, $loanId]);

                    // Create a notification for the borrower
                    $notificationMessage = "✅ Partial payment of {$paymentAmount} coins made for loan #{$loanId}. Remaining balance: {$remaining} coins.";
                    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                    $stmt->execute([$userId, $notificationMessage]);
					
					// Notification for lender
					$lenderMsg = "💸 A partial payment of {$paymentAmount} coins was received for loan #{$loanId}.";
					$stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
					$stmt->execute([$loan['funded_by'], $lenderMsg]);
                }

                // Update borrower balance
                $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$paymentAmount, $userId]);

                // Credit lender
                $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$paymentAmount, $loan['funded_by']]);

                $message = "✅ Payment of {$paymentAmount} coins made successfully!";
            }
        }
    }
}

// Fetch active loans
$stmt = $pdo->prepare("SELECT * FROM loans WHERE borrower_id = ? AND status = 'active'");
$stmt->execute([$userId]);
$activeLoans = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Loans</title>
    <link rel="stylesheet" href="assets/css/payloan.css">
</head>
<body>

<div class="container">
    <h2>💳 Repay Active Loans</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if (count($activeLoans) === 0): ?>
        <p>No active loans to repay.</p>
    <?php endif; ?>

    <?php foreach ($activeLoans as $loan): ?>
        <div class="loan-card">
            <form method="POST">
                <p><strong>Loan #<?= $loan['id'] ?></strong></p>
                <p><strong>Remaining Amount:</strong> <?= number_format($loan['amount'], 2) ?> coins</p>

                <label for="payment_amount">Pay Amount:</label>
                <input type="number" name="payment_amount" step="0.01" min="0.01" max="<?= $loan['amount'] ?>" required>

                <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                <button type="submit">Pay</button>
            </form>
        </div>
    <?php endforeach; ?>

    <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
