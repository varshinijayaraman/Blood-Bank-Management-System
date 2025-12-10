<?php
session_start();
require_once 'db/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    switch ($user_type) {
        case 'admin':
            $query = "SELECT * FROM admin_users WHERE username = ? AND password = ?";
            break;
        case 'donor':
            $query = "SELECT * FROM donors WHERE username = ? AND password = ?";
            break;
        case 'recipient':
            $query = "SELECT * FROM recipients WHERE username = ? AND password = ?";
            break;
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION[$user_type . '_id'] = $row['id'];
        $_SESSION['user_type'] = $user_type;
        header("Location: $user_type/dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Video background */
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

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }

        .form-control, .btn {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #e74c3c;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #c0392b;
        }

        .alert {
            margin-bottom: 10px;
            color: #e74c3c;
        }

        .footer {
            text-align: center;
            color: #fff;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .navbar {
            padding: 1rem;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .logo {
            color: #fff;
            font-size: 1.5rem;
            text-decoration: none;
        }

        .register-link a {
            color: #e74c3c;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Video Background -->
    <video autoplay muted loop class="video-bg">
        <source src="videos/blood_donation.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Blood Bank Management
            </a>
        </nav>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="card">
                <h2>Login</h2>
                <?php if (isset($error)) { ?>
                    <div class="alert"><?php echo $error; ?></div>
                <?php } ?>
                <form action="login_portal.php" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="user_type">Login As:</label>
                        <select id="user_type" name="user_type" required class="form-control">
                            <option value="admin">Admin</option>
                            <option value="donor">Donor</option>
                            <option value="recipient">Recipient</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <div class="register-link">
                    <p>Don't have an account? <a href="register_portal.php">Register here</a></p>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
    </footer>
</body>
</html>
