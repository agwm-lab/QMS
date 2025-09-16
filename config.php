<?php
$host = "localhost";
$user = "root";      // adjust if needed
$pass = "";
$db   = "bank_queue";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
