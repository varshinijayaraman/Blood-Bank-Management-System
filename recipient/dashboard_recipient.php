<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'blood_bank');
if (!isset($_SESSION['recipient'])) {
    header("Location: login_recipient.php");
    exit();
}
$username = $_SESSION['recipient'];
$result = mysqli_query($conn, "SELECT * FROM recipients WHERE username = '$username'");
$recipient = mysqli_fetch_assoc($result);
$id = $recipient['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group = $_POST['blood_group'];
    $units = $_POST['units'];
    mysqli_query($conn, "INSERT INTO requests (recipient_id, blood_group, units_requested) VALUES ($id, '$group', $units)");
    echo "Request submitted!";
}

$request_status = mysqli_query($conn, "SELECT * FROM requests WHERE recipient_id = $id");

echo "<h2>Recipient Dashboard</h2>";
echo "<form method='post'>
    Blood Group: <input type='text' name='blood_group'><br>
    Units Needed: <input type='number' name='units'><br>
    <input type='submit' value='Submit Request'>
</form>";

echo "<h3>Your Requests</h3>";
while ($row = mysqli_fetch_assoc($request_status)) {
    echo "Request ID: {$row['id']} - Group: {$row['blood_group']} - Units: {$row['units_requested']} - Status: {$row['status']}<br>";
}
?>
<a href='logout.php'>Logout</a>
