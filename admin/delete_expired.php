<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blood_group = $_POST['blood_group'];

    // Get count of expired units
    $count_query = "SELECT COUNT(*) as expired_count 
                   FROM inventory 
                   WHERE blood_group = ? AND expiry_date < CURDATE()";
    $stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt, "s", $blood_group);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result);

    if ($count['expired_count'] > 0) {
        // Delete expired units
        $delete_query = "DELETE FROM inventory 
                        WHERE blood_group = ? AND expiry_date < CURDATE()";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "s", $blood_group);
        
        if (mysqli_stmt_execute($stmt)) {
            // Record transaction
            $transaction_query = "INSERT INTO transactions (action, actor, details) 
                                VALUES ('deletion', 'admin', 'Admin deleted {$count['expired_count']} expired units of $blood_group blood')";
            mysqli_query($conn, $transaction_query);

            $_SESSION['success'] = "Successfully deleted {$count['expired_count']} expired units of $blood_group blood.";
        } else {
            $_SESSION['error'] = "Failed to delete expired units. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No expired units found for this blood group.";
    }

    header("Location: dashboard.php");
    exit();
}
?> 