<?php
session_start();
require_once 'db/db_connect.php';

// Fetch blood inventory
$query = "SELECT blood_group, 
          COALESCE(SUM(available_units), 0) as total_units, 
          COUNT(CASE WHEN expiry_date < CURDATE() THEN 1 END) as expired_units 
          FROM inventory 
          GROUP BY blood_group";
$result = mysqli_query($conn, $query);

// If no blood groups exist in inventory, create entries for all blood groups with 0 units
if (mysqli_num_rows($result) == 0) {
    $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    foreach ($blood_groups as $group) {
        $insert_query = "INSERT INTO inventory (blood_group, available_units, expiry_date) 
                        VALUES ('$group', 0, DATE_ADD(CURDATE(), INTERVAL 35 DAY))";
        mysqli_query($conn, $insert_query);
    }
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Video background styling */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .video-bg {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
        }

        .container {
            position: relative;
            z-index: 1;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }

        .header .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: rgba(0,0,0,0.6);
            color: white;
        }

        .logo {
            color: #fff;
            font-size: 1.8rem;
            text-decoration: none;
        }

        .nav-links a {
            color: #fff;
            margin-left: 1rem;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background-color: #e74c3c;
            border-radius: 5px;
        }

        .nav-links a:hover {
            background-color: #c0392b;
        }

        .hero-section {
            text-align: center;
            padding: 2rem 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background-color: #ffffffd0;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .features-section {
            margin-top: 3rem;
            text-align: center;
        }

        .features-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 1rem;
        }

        .feature-card {
            flex: 1 1 250px;
            background-color: #ffffffd0;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .feature-card i {
            font-size: 2rem;
            color: #e74c3c;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 0.8rem;
            border: 1px solid #ccc;
        }

        .status-available {
            color: green;
            font-weight: bold;
        }

        .status-unavailable {
            color: red;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
        }
    </style>
</head>
<body>

    <!-- Video Background -->
    <video autoplay muted loop class="video-bg">
        <source src="videos/blood_drop.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Blood Bank Management
            </a>
            <div class="nav-links">
                <a href="login_portal.php" class="btn btn-primary">Login</a>
                <a href="register_portal.php" class="btn btn-primary">Register</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="hero-section">
            <h1>Welcome to Blood Bank Management System</h1>
            <p>Donate blood, save lives. Your contribution can make a difference.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Blood Inventory Status</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Blood Group</th>
                            <th>Available Units</th>
                            <th>Expired Units</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['blood_group']; ?></td>
                                <td><?php echo $row['total_units']; ?></td>
                                <td><?php echo $row['expired_units']; ?></td>
                                <td>
                                    <?php 
                                    if($row['total_units'] > 0) {
                                        echo '<span class="status-available">Available</span>';
                                    } else {
                                        echo '<span class="status-unavailable">Unavailable</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Blood Safety Information</h2>
                <div class="safety-info">
                    <ul>
                        <li><i class="fas fa-check-circle"></i> All blood units are tested for infectious diseases</li>
                        <li><i class="fas fa-check-circle"></i> Regular quality checks are performed</li>
                        <li><i class="fas fa-check-circle"></i> Proper storage conditions are maintained</li>
                        <li><i class="fas fa-check-circle"></i> Expiry dates are strictly monitored</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="features-section">
            <h2>Why Donate Blood?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-heart"></i>
                    <h3>Save Lives</h3>
                    <p>Your blood donation can save up to 3 lives</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Quick Process</h3>
                    <p>Donation process takes only 30-45 minutes</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Secure</h3>
                    <p>All donations are handled with utmost care</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
    </footer>
</body>
</html>
