<?php
require_once 'db_connect.php';

$sql = "ALTER TABLE requests 
        MODIFY COLUMN hospital VARCHAR(100) NULL,
        MODIFY COLUMN doctor_contact VARCHAR(15) NULL";

if (mysqli_query($conn, $sql)) {
    echo "Requests table updated successfully";
} else {
    echo "Error updating requests table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 