<?php
// Database connection settings
$host = "localhost";      // Database host (keep as localhost if using XAMPP/WAMP)
$user = "root";           // Default MySQL user in XAMPP
$pass = "";               // Default is empty for XAMPP (set password if you changed it)
$dbname = "bank_queue"; // Database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: set default timezone (important for tickets)
date_default_timezone_set("Asia/Manila");
?>
