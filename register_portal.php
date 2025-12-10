<?php
session_start();
require_once 'db/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Phone number validation (backend)
    if (!preg_match('/^\d{10}$/', $contact)) {
        $error = "Contact number must be exactly 10 digits.";
    } else {
        // Check if username already exists
        $check_query = "SELECT * FROM " . ($user_type == 'donor' ? 'donors' : 'recipients') . " WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username already exists";
        } else {
            if ($user_type == 'donor') {
                $blood_group = $_POST['blood_group'];
                $age = $_POST['age'];
                $gender = $_POST['gender'];
                $address = $_POST['address'];

                $query = "INSERT INTO donors (username, password, name, age, gender, blood_group, contact, email, address) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssisssss", $username, $password, $name, $age, $gender, $blood_group, $contact, $email, $address);
            } else {
                $blood_group = $_POST['blood_group'];
                $age = $_POST['age'];
                // Hospital and doctor_contact removed
                $query = "INSERT INTO recipients (username, password, name, age, blood_group, contact, email) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssisss", $username, $password, $name, $age, $blood_group, $contact, $email);
            }

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login_portal.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Bank Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-heartbeat"></i> Blood Bank Management
            </a>
        </nav>
    </header>

    <div class="container">
        <div class="register-container">
            <div class="card">
                <h2>Register</h2>
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <form action="register_portal.php" method="POST" class="register-form">
                    <div class="form-group">
                        <label for="user_type">Register As:</label>
                        <select id="user_type" name="user_type" required class="form-control">
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
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" id="age" name="age" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="blood_group">Blood Group:</label>
                        <select id="blood_group" name="blood_group" required class="form-control">
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number:</label>
                        <input type="tel" id="contact" name="contact" required class="form-control" pattern="\d{10}" maxlength="10" title="Enter a 10-digit phone number">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>

                    <!-- Donor specific fields -->
                    <div id="donor-fields">
                        <div class="form-group">
                            <label for="gender">Gender:</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <textarea id="address" name="address" class="form-control"></textarea>
                        </div>
                    </div>

                    <!-- Recipient specific fields (hospital and doctor_contact removed) -->
                    <div id="recipient-fields" style="display: none;"></div>

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                <div class="login-link">
                    <p>Already have an account? <a href="login_portal.php">Login here</a></p>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Blood Bank Management System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('user_type').addEventListener('change', function() {
            const donorFields = document.getElementById('donor-fields');
            const recipientFields = document.getElementById('recipient-fields');
            
            if (this.value === 'donor') {
                donorFields.style.display = 'block';
                recipientFields.style.display = 'none';
            } else {
                donorFields.style.display = 'none';
                recipientFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
