<?php
include 'config.php';
date_default_timezone_set('Asia/Manila');

// Example: Teller = A, Loans = B, Customer Service = C
$servicePrefix = isset($_GET['service']) ? $_GET['service'] : "A";

// Generate client number
$result = $conn->query("SELECT COUNT(*) AS total FROM tickets WHERE DATE(created_at)=CURDATE()");
$row = $result->fetch_assoc();
$nextNumber = str_pad($row['total'] + 1, 3, "0", STR_PAD_LEFT);
$clientNo = $servicePrefix . "-" . $nextNumber;

// Insert ticket into DB
$stmt = $conn->prepare("INSERT INTO tickets (client_no, service_type) VALUES (?, ?)");
$stmt->bind_param("ss", $clientNo, $servicePrefix);
$stmt->execute();

$ticketId = $stmt->insert_id;
$stmt->close();

$date = date("m/d/Y");
$time = date("h:i A");
$windowNo = "TBD"; // Assigned later by staff
?>
<!DOCTYPE html>
<html>
<head>
    <title>Queue Ticket</title>
    <style>
        body { font-family: monospace; font-size: 14px; }
        pre  { white-space: pre; }
    </style>
    <script>
        window.onload = function() { window.print(); }
    </script>
</head>
<body>
<pre>
========================================
             TREASURY OFFICE
========================================

   Window #:   <?php echo $windowNo; ?>

   Client No.: <?php echo $clientNo; ?>

   Date:       <?php echo $date; ?>

   Time:       <?php echo $time; ?>


========================================
 Please wait for your turn.
 Watch the display screen.
========================================
</pre>
</body>
</html>
