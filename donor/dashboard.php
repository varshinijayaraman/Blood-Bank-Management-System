<?php
session_start();
require_once '../db/db_connect.php';

// Check if donor is logged in
if (!isset($_SESSION['donor_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

$donor_id = $_SESSION['donor_id'];

// Fetch donor details
$donor_query = "SELECT * FROM donors WHERE id = $donor_id";
$donor_result = mysqli_query($conn, $donor_query);
$donor = mysqli_fetch_assoc($donor_result);

// Fetch donation history
$history_query = "SELECT * FROM transactions WHERE actor = 'donor' AND details LIKE '%$donor_id%' ORDER BY date_time DESC";
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Donor Dashboard
            </a>
            <div class="nav-links">
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-grid">
            <!-- Donor Profile -->
            <div class="card">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?php echo $donor['name']; ?></p>
                    <p><strong>Age:</strong> <?php echo $donor['age']; ?></p>
                    <p><strong>Gender:</strong> <?php echo $donor['gender']; ?></p>
                    <p><strong>Email:</strong> <?php echo $donor['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $donor['contact']; ?></p>
                    <p><strong>Blood Group:</strong> <?php echo $donor['blood_group']; ?></p>
                    <p><strong>Address:</strong> <?php echo $donor['address']; ?></p>
                    <p><strong>Last Donation:</strong> <?php echo $donor['last_donation_date'] ? $donor['last_donation_date'] : 'Never'; ?></p>
                </div>
            </div>

            <!-- Donation Form -->
            <div class="card">
                <h2>Make a Donation</h2>
                <form action="process_donation.php" method="POST" class="donation-form">
                    <div class="form-group">
                        <label for="units">Number of Units:</label>
                        <input type="number" id="units" name="units" min="1" max="2" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="donation_date">Donation Date:</label>
                        <input type="date" id="donation_date" name="donation_date" required class="form-control">
                    </div>
                    <div class="form-group" style="display:none;">
                        <!-- Hidden phone field for validation, not shown but required for HTML5 validation -->
                        <input type="tel" id="contact" name="contact" pattern="\d{10}" maxlength="10" title="Enter a 10-digit phone number" value="<?php echo $donor['contact']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Donation</button>
                </form>
            </div>

            <!-- Donation History -->
            <div class="card">
                <h2>Your Donation History</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($history_result)) { ?>
                            <tr>
                                <td><?php echo $row['date_time']; ?></td>
                                <td><?php echo $row['action']; ?></td>
                                <td><?php echo $row['details']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Blood Safety Information -->
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
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 