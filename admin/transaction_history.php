<?php
session_start();
require_once '../db/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

// Fetch all transactions
$transaction_query = "SELECT * FROM transactions ORDER BY date_time DESC";
$transaction_result = mysqli_query($conn, $transaction_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .transaction-card {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .transaction-time {
            color: #666;
            font-size: 0.9rem;
        }
        .transaction-action {
            font-weight: 600;
            color: #e74c3c;
        }
        .transaction-details {
            color: #333;
            margin-top: 0.5rem;
        }
        .transaction-actor {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-heartbeat"></i> Transaction History
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h2>Transaction History</h2>
            <?php while($row = mysqli_fetch_assoc($transaction_result)) { ?>
                <div class="transaction-card">
                    <div class="transaction-header">
                        <span class="transaction-action"><?php echo ucfirst($row['action']); ?></span>
                        <span class="transaction-time"><?php echo $row['date_time']; ?></span>
                    </div>
                    <div class="transaction-details"><?php echo $row['details']; ?></div>
                    <div class="transaction-actor">By: <?php echo ucfirst($row['actor']); ?></div>
                </div>
            <?php } ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 