<?php
session_start();
require_once '../db/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

// Fetch blood status with detailed information
$status_query = "SELECT 
    blood_group,
    SUM(available_units) as total_units,
    SUM(CASE WHEN expiry_date < CURDATE() THEN available_units ELSE 0 END) as expired_units,
    SUM(CASE WHEN expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN available_units ELSE 0 END) as expiring_soon,
    MIN(expiry_date) as nearest_expiry,
    MAX(expiry_date) as furthest_expiry
    FROM inventory 
    GROUP BY blood_group";
$status_result = mysqli_query($conn, $status_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Status - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .status-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .status-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e74c3c;
        }
        .status-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .status-item {
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .status-item span {
            display: block;
            font-size: 0.9rem;
            color: #666;
        }
        .status-item strong {
            display: block;
            font-size: 1.1rem;
            color: #333;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-heartbeat"></i> Blood Status
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <?php while($row = mysqli_fetch_assoc($status_result)) { ?>
            <div class="status-card">
                <div class="status-header">
                    <div class="status-title"><?php echo $row['blood_group']; ?> Blood Group</div>
                    <div class="status-value"><?php echo $row['total_units']; ?> Units</div>
                </div>
                <div class="status-details">
                    <div class="status-item">
                        <span>Available Units</span>
                        <strong><?php echo $row['total_units']; ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Expired Units</span>
                        <strong><?php echo $row['expired_units']; ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Expiring Soon (7 days)</span>
                        <strong><?php echo $row['expiring_soon']; ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Nearest Expiry</span>
                        <strong><?php echo $row['nearest_expiry']; ?></strong>
                    </div>
                    <div class="status-item">
                        <span>Furthest Expiry</span>
                        <strong><?php echo $row['furthest_expiry']; ?></strong>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 