<?php
session_start();
require_once '../db/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_portal.php");
    exit();
}

// Update status on action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $status = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';
    $id = intval($_POST['request_id']);
    mysqli_query($conn, "UPDATE requests SET status = '$status' WHERE id = $id");
}

// Fetch requests
$query = "SELECT r.*, rec.name AS recipient_name 
          FROM requests r 
          JOIN recipients rec ON r.recipient_id = rec.id 
          ORDER BY r.date_time DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f7f9fc;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    thead {
        background-color: #3498db;
        color: white;
    }

    th, td {
        padding: 12px 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tbody tr:hover {
        background-color: #e6f7ff;
    }

    .btn {
        padding: 6px 14px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #27ae60;
        color: white;
    }

    .btn-success:hover {
        background-color: #219150;
    }

    .btn-danger {
        background-color: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }

    .btn-secondary {
        background-color: #7f8c8d;
        color: white;
        padding: 8px 16px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        border-radius: 4px;
    }

    .btn-secondary:hover {
        background-color: #707b7c;
    }

    .status-approved {
        color: green;
        font-weight: bold;
    }

    .status-rejected {
        color: red;
        font-weight: bold;
    }

    .status-pending {
        color: orange;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        th, td {
            font-size: 12px;
            padding: 8px;
        }

        .btn {
            padding: 5px 10px;
            font-size: 12px;
        }
    }
</style>
</head>
<body>
    <h2>Manage Blood Requests</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Recipient</th>
                <th>Blood Group</th>
                <th>Units</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['recipient_name'] ?></td>
                    <td><?= $row['blood_group'] ?></td>
                    <td><?= $row['units_requested'] ?></td>
                    <td><?= $row['date_time'] ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending') { ?>
                            <form method="post">
                                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        <?php } else {
                            echo ucfirst($row['status']);
                        } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</body>
</html>
