<?php
session_start();
require_once '../db/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

// Fetch all donor registrations
$donor_query = "SELECT * FROM donors ORDER BY id DESC";
$donor_result = mysqli_query($conn, $donor_query);

// Fetch all blood requests
$request_query = "SELECT r.*, rec.name as recipient_name, rec.contact as recipient_contact 
                 FROM requests r 
                 JOIN recipients rec ON r.recipient_id = rec.id 
                 ORDER BY r.id DESC";
$request_result = mysqli_query($conn, $request_query);

// Fetch blood inventory
$inventory_query = "SELECT blood_group, 
                   SUM(available_units) as total_units, 
                   COUNT(CASE WHEN expiry_date < CURDATE() THEN 1 END) as expired_units,
                   MIN(expiry_date) as nearest_expiry
                   FROM inventory 
                   GROUP BY blood_group";
$inventory_result = mysqli_query($conn, $inventory_query);

// Fetch all transactions
$transaction_query = "SELECT * FROM transactions ORDER BY date_time DESC";
$transaction_result = mysqli_query($conn, $transaction_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }
        .dashboard-button {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            text-decoration: none;
            color: #333;
        }
        .dashboard-button:hover {
            transform: translateY(-5px);
        }
        .dashboard-button i {
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        .dashboard-button h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }
        .dashboard-button p {
            margin: 0.5rem 0 0;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Admin Dashboard
            </a>
            <div class="nav-links">
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-buttons">
            <a href="blood_inventory.php" class="dashboard-button">
                <i class="fas fa-vial"></i>
                <h3>Blood Inventory</h3>
                <p>Manage blood stock and inventory</p>
            </a>
            
            <a href="blood_status.php" class="dashboard-button">
                <i class="fas fa-chart-line"></i>
                <h3>Blood Status</h3>
                <p>View blood availability and expiry status</p>
            </a>
            
            <a href="donor_registrations.php" class="dashboard-button">
                <i class="fas fa-user-plus"></i>
                <h3>Donor Registrations</h3>
                <p>View and manage donor information</p>
            </a>
            
            <a href="transaction_history.php" class="dashboard-button">
                <i class="fas fa-history"></i>
                <h3>Transaction History</h3>
                <p>View all system transactions</p>
            </a>
            <a href="manage_requests.php" class="dashboard-button">
                <i class="fas fa-hand-holding-medical"></i>
                <h3>Blood Requests</h3>
                <p>Approve or reject recipient blood requests</p>
            </a>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 