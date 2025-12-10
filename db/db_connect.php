<?php
$host = 'localhost';
$username = 'root';
$password = '';

// First connect without database
$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS blood_bank";
mysqli_query($conn, $sql);

// Select the database
mysqli_select_db($conn, "blood_bank");

// Create tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS admin_users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS donors (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        age INT NOT NULL,
        gender ENUM('Male', 'Female', 'Other') NOT NULL,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        contact VARCHAR(15) NOT NULL,
        email VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        last_donation_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS recipients (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        age INT NOT NULL,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        contact VARCHAR(15) NOT NULL,
        email VARCHAR(100) NOT NULL,
        hospital VARCHAR(100) NOT NULL,
        doctor_contact VARCHAR(15) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS inventory (
        id INT PRIMARY KEY AUTO_INCREMENT,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        available_units INT NOT NULL,
        expiry_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        recipient_id INT NOT NULL,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        units_requested INT NOT NULL,
        hospital VARCHAR(100) NOT NULL,
        doctor_contact VARCHAR(15) NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipient_id) REFERENCES recipients(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS transactions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        action VARCHAR(50) NOT NULL,
        actor VARCHAR(50) NOT NULL,
        details TEXT NOT NULL,
        date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

// Execute each table creation query
foreach ($tables as $sql) {
    mysqli_query($conn, $sql);
}

// Check if admin user exists, if not create default admin
$check_admin = "SELECT * FROM admin_users WHERE username = 'admin'";
$result = mysqli_query($conn, $check_admin);
if (mysqli_num_rows($result) == 0) {
    $admin_sql = "INSERT INTO admin_users (username, password) VALUES ('admin', 'admin123')";
    mysqli_query($conn, $admin_sql);
}

// Set charset to ensure proper encoding
mysqli_set_charset($conn, "utf8mb4");
?> 