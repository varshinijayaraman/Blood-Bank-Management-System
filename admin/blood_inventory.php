<?php
session_start();
require_once '../db/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

// Fetch blood inventory
$inventory_query = "SELECT blood_group, 
                   SUM(available_units) as total_units, 
                   COUNT(CASE WHEN expiry_date < CURDATE() THEN 1 END) as expired_units,
                   MIN(expiry_date) as nearest_expiry
                   FROM inventory 
                   GROUP BY blood_group";
$inventory_result = mysqli_query($conn, $inventory_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Inventory - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-heartbeat"></i> Blood Inventory
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h2>Blood Inventory Status</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Blood Group</th>
                        <th>Available Units</th>
                        <th>Expired Units</th>
                        <th>Nearest Expiry</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($inventory_result)) { ?>
                        <tr>
                            <td><?php echo $row['blood_group']; ?></td>
                            <td><?php echo $row['total_units']; ?></td>
                            <td><?php echo $row['expired_units']; ?></td>
                            <td><?php echo $row['nearest_expiry']; ?></td>
                            <td>
                                <?php if($row['expired_units'] > 0) { ?>
                                    <form action="delete_expired.php" method="POST">
                                        <input type="hidden" name="blood_group" value="<?php echo $row['blood_group']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete Expired</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 