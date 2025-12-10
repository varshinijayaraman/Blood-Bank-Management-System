<?php
session_start();
require_once '../db/db_connect.php';

// Check if recipient is logged in
if (!isset($_SESSION['recipient_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

$recipient_id = $_SESSION['recipient_id'];

// Fetch recipient details
$recipient_query = "SELECT * FROM recipients WHERE id = $recipient_id";
$recipient_result = mysqli_query($conn, $recipient_query);
$recipient = mysqli_fetch_assoc($recipient_result);

// Fetch recipient's requests
$request_query = "SELECT * FROM requests WHERE recipient_id = $recipient_id ORDER BY id DESC";
$request_result = mysqli_query($conn, $request_query);

// Fetch blood inventory for available blood groups
$inventory_query = "SELECT blood_group, SUM(available_units) as total_units 
                   FROM inventory 
                   WHERE expiry_date > CURDATE() 
                   GROUP BY blood_group";
$inventory_result = mysqli_query($conn, $inventory_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipient Dashboard - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="../index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Recipient Dashboard
            </a>
            <div class="nav-links">
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="dashboard-grid">
            <!-- Recipient Profile -->
            <div class="card">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?php echo $recipient['name']; ?></p>
                    <p><strong>Age:</strong> <?php echo $recipient['age']; ?></p>
                    <p><strong>Blood Group:</strong> <?php echo $recipient['blood_group']; ?></p>
                    <p><strong>Contact:</strong> <?php echo $recipient['contact']; ?></p>
                </div>
            </div>

            <!-- Blood Request Form -->
            <div class="card">
                <h2>Request Blood</h2>
                <form action="process_request.php" method="POST" class="request-form">
                    <div class="form-group">
                        <label for="blood_group">Blood Group Required:</label>
                        <select id="blood_group" name="blood_group" required class="form-control">
                            <option value="">Select Blood Group</option>
                            <?php while($row = mysqli_fetch_assoc($inventory_result)) { ?>
                                <option value="<?php echo $row['blood_group']; ?>">
                                    <?php echo $row['blood_group']; ?> (Available: <?php echo $row['total_units']; ?> units)
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="units">Number of Units Needed:</label>
                        <input type="number" id="units" name="units" min="1" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>

            <!-- Request History -->
            <div class="card">
                <h2>Your Request History</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Blood Group</th>
                            <th>Units</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($request_result)) { ?>
                            <tr>
                                <td><?php echo $row['date_time']; ?></td>
                                <td><?php echo $row['blood_group']; ?></td>
                                <td><?php echo $row['units_requested']; ?></td>
                                <td>
                                    <span class="status-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Collection Instructions -->
            <div class="card">
                <h2>Blood Collection Instructions</h2>
                <div class="instructions">
                    <ul>
                        <li><i class="fas fa-info-circle"></i> Once your request is approved, you can collect the blood from our blood bank</li>
                        <li><i class="fas fa-info-circle"></i> Please bring a valid ID proof and the approval notification</li>
                        <li><i class="fas fa-info-circle"></i> Collection hours: 9:00 AM to 5:00 PM</li>
                        <li><i class="fas fa-info-circle"></i> For emergency cases, please contact our 24/7 helpline</li>
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