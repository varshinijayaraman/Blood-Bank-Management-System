<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = $_SESSION['donor_id'];
    $units = $_POST['units'];
    $donation_date = $_POST['donation_date'];

    // Get donor details
    $donor_query = "SELECT * FROM donors WHERE id = $donor_id";
    $donor_result = mysqli_query($conn, $donor_query);
    $donor = mysqli_fetch_assoc($donor_result);

    // Calculate expiry date (35 days from donation)
    $expiry_date = date('Y-m-d', strtotime($donation_date . ' + 35 days'));

    // Insert into inventory
    $inventory_query = "INSERT INTO inventory (blood_group, available_units, expiry_date) 
                       VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $inventory_query);
    mysqli_stmt_bind_param($stmt, "sis", $donor['blood_group'], $units, $expiry_date);

    if (mysqli_stmt_execute($stmt)) {
        // Update donor's last donation date
        $update_query = "UPDATE donors SET last_donation_date = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $donation_date, $donor_id);
        mysqli_stmt_execute($stmt);

        // Record transaction
        $transaction_query = "INSERT INTO transactions (action, actor, details) 
                            VALUES ('donation', 'donor', 'Donor ID: $donor_id donated $units units of {$donor['blood_group']} blood')";
        mysqli_query($conn, $transaction_query);

        $_SESSION['success'] = "Donation recorded successfully!";
    } else {
        $_SESSION['error'] = "Failed to record donation. Please try again.";
    }

    header("Location: dashboard.php");
    exit();
}
?> 