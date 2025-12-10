<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    // Get request details
    $request_query = "SELECT * FROM requests WHERE id = ?";
    $stmt = mysqli_prepare($conn, $request_query);
    mysqli_stmt_bind_param($stmt, "i", $request_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $request = mysqli_fetch_assoc($result);

    if ($action == 'approve') {
        // Check if enough blood is available
        $inventory_query = "SELECT SUM(available_units) as total_units 
                           FROM inventory 
                           WHERE blood_group = ? AND expiry_date > CURDATE()";
        $stmt = mysqli_prepare($conn, $inventory_query);
        mysqli_stmt_bind_param($stmt, "s", $request['blood_group']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $inventory = mysqli_fetch_assoc($result);

        if ($inventory['total_units'] >= $request['units_requested']) {
            // Update request status
            $update_query = "UPDATE requests SET status = 'approved' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "i", $request_id);
            mysqli_stmt_execute($stmt);

            // Update inventory
            $update_inventory = "UPDATE inventory 
                               SET available_units = available_units - ? 
                               WHERE blood_group = ? AND expiry_date > CURDATE() 
                               ORDER BY expiry_date ASC 
                               LIMIT ?";
            $stmt = mysqli_prepare($conn, $update_inventory);
            mysqli_stmt_bind_param($stmt, "isi", $request['units_requested'], $request['blood_group'], $request['units_requested']);
            mysqli_stmt_execute($stmt);

            // Record transaction
            $transaction_query = "INSERT INTO transactions (action, actor, details) 
                                VALUES ('approval', 'admin', 'Admin approved request ID: $request_id for {$request['units_requested']} units of {$request['blood_group']} blood')";
            mysqli_query($conn, $transaction_query);

            $_SESSION['success'] = "Request approved successfully!";
        } else {
            $_SESSION['error'] = "Not enough blood units available to approve this request.";
        }
    } else if ($action == 'reject') {
        // Update request status
        $update_query = "UPDATE requests SET status = 'rejected' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $request_id);
        mysqli_stmt_execute($stmt);

        // Record transaction
        $transaction_query = "INSERT INTO transactions (action, actor, details) 
                            VALUES ('rejection', 'admin', 'Admin rejected request ID: $request_id')";
        mysqli_query($conn, $transaction_query);

        $_SESSION['success'] = "Request rejected successfully!";
    }

    header("Location: dashboard.php");
    exit();
}
?> 