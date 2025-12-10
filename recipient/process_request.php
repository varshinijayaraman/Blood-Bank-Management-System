<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['recipient_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipient_id = $_SESSION['recipient_id'];
    $blood_group = $_POST['blood_group'];
    $units = $_POST['units'];

    // Check if enough blood is available
    $inventory_query = "SELECT SUM(available_units) as total_units 
                       FROM inventory 
                       WHERE blood_group = ? AND expiry_date > CURDATE()";
    $stmt = mysqli_prepare($conn, $inventory_query);
    mysqli_stmt_bind_param($stmt, "s", $blood_group);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $inventory = mysqli_fetch_assoc($result);

    if ($inventory['total_units'] >= $units) {
        // Insert request
        $request_query = "INSERT INTO requests (recipient_id, blood_group, units_requested, status) 
                         VALUES (?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $request_query);
        mysqli_stmt_bind_param($stmt, "isi", $recipient_id, $blood_group, $units);

        if (mysqli_stmt_execute($stmt)) {
            // Record transaction
            $transaction_query = "INSERT INTO transactions (action, actor, details) 
                                VALUES ('request', 'recipient', 'Recipient ID: $recipient_id requested $units units of $blood_group blood')";
            mysqli_query($conn, $transaction_query);

            $_SESSION['success'] = "Blood request submitted successfully!";
        } else {
            $_SESSION['error'] = "Failed to submit request. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Sorry, not enough blood units available. Please try again later.";
    }

    header("Location: dashboard.php");
    exit();
}
?> 