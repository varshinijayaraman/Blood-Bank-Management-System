<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'blood_bank');
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

// Fetch donors
$donors = mysqli_query($conn, "SELECT name, blood_group, last_donation_date FROM donors");

// Fetch requests
$requests = mysqli_query($conn, "SELECT * FROM requests");

// Fetch inventory
$inventory = mysqli_query($conn, "SELECT * FROM inventory");

// Delete expired blood units
mysqli_query($conn, "DELETE FROM inventory WHERE expiry_date < CURDATE()");

echo "<h2>Admin Dashboard</h2>";
echo "<h3>Donor Registrations</h3>";
while ($row = mysqli_fetch_assoc($donors)) {
    echo "Name: {$row['name']} - Blood Group: {$row['blood_group']} - Last Donation: {$row['last_donation_date']}<br>";
}

echo "<h3>Blood Requests</h3>";
echo "<a href='manage_requests.php'>Go to Blood Request Management</a><br><br>";
while ($row = mysqli_fetch_assoc($requests)) {
    echo "Request ID: {$row['id']} - Blood Group: {$row['blood_group']} - Units: {$row['units_requested']} - Status: {$row['status']}<br>";
}

echo "<h3>Blood Inventory</h3>";
while ($row = mysqli_fetch_assoc($inventory)) {
    echo "Blood Group: {$row['blood_group']} - Units: {$row['available_units']} - Expiry: {$row['expiry_date']}<br>";
}
?>
<a href='logout.php'>Logout</a>
