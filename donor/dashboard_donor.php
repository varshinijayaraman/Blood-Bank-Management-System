<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'blood_bank');
if (!isset($_SESSION['donor'])) {
    header("Location: login_donor.php");
    exit();
}
$username = $_SESSION['donor'];
$result = mysqli_query($conn, "SELECT * FROM donors WHERE username = '$username'");
$donor = mysqli_fetch_assoc($result);

echo "<h2>Welcome, {$donor['name']}</h2>";
echo "Age: {$donor['age']}<br>";
echo "Gender: {$donor['gender']}<br>";
echo "Email: {$donor['username']}<br>";
echo "Phone: {$donor['contact']}<br>";
echo "Blood Group: {$donor['blood_group']}<br>";
echo "Last Donation Date: {$donor['last_donation_date']}<br>";

echo "<form method='post'>
    <input type='submit' name='donate' value='Add Donation'>
</form>";

if (isset($_POST['donate'])) {
    $bg = $donor['blood_group'];
    $expiry = date('Y-m-d', strtotime('+42 days'));
    mysqli_query($conn, "INSERT INTO inventory (blood_group, available_units, expiry_date) VALUES ('$bg', 1, '$expiry')");
    $today = date('Y-m-d');
    mysqli_query($conn, "UPDATE donors SET last_donation_date = '$today' WHERE username = '$username'");
    echo "Donation recorded successfully!";
}
?>
<a href='logout.php'>Logout</a>
