<?php
session_start();
include("db.php");

// ✅ Only admins can access reports
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Date filters
$today = date("Y-m-d");
$start_week = date("Y-m-d", strtotime("monday this week"));
$end_week = date("Y-m-d", strtotime("sunday this week"));
$start_month = date("Y-m-01");
$end_month = date("Y-m-t");

// Helper function
function getCount($conn, $where) {
    $sql = "SELECT COUNT(*) as total FROM tickets WHERE $where";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total'];
}

// ✅ Daily report
$total_today = getCount($conn, "DATE(created_at) = CURDATE()");
$waiting_today = getCount($conn, "status='waiting' AND DATE(created_at) = CURDATE()");
$serving_today = getCount($conn, "status='serving' AND DATE(created_at) = CURDATE()");
$done_today = getCount($conn, "status='done' AND DATE(created_at) = CURDATE()");

// ✅ Weekly report
$total_week = getCount($conn, "DATE(created_at) BETWEEN '$start_week' AND '$end_week'");

// ✅ Monthly report
$total_month = getCount($conn, "DATE(created_at) BETWEEN '$start_month' AND '$end_month'");

// ✅ Detailed records (today’s tickets)
$details = $conn->query("SELECT * FROM tickets WHERE DATE(created_at) = CURDATE() ORDER BY created_at ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Reports</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #2c3e50; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f4f4f4; }
        .summary { margin-bottom: 30px; }
    </style>
</head>
<body>
    <h1>Queue Reports</h1>
    <p>Welcome, <strong><?php echo $_SESSION['username']; ?></strong> | <a href="admin_dashboard.php">Back to Dashboard</a></p>
    <hr>

    <div class="summary">
        <h2>Daily Report (<?php echo $today; ?>)</h2>
        <p>Total Tickets: <strong><?php echo $total_today; ?></strong></p>
        <p>Waiting: <strong><?php echo $waiting_today; ?></strong> | Serving: <strong><?php echo $serving_today; ?></strong> | Done: <strong><?php echo $done_today; ?></strong></p>
    </div>

    <div class="summary">
        <h2>Weekly Report (<?php echo $start_week; ?> → <?php echo $end_week; ?>)</h2>
        <p>Total Tickets: <strong><?php echo $total_week; ?></strong></p>
    </div>

    <div class="summary">
        <h2>Monthly Report (<?php echo $start_month; ?> → <?php echo $end_month; ?>)</h2>
        <p>Total Tickets: <strong><?php echo $total_month; ?></strong></p>
    </div>

    <h2>Detailed Daily Report</h2>
    <table>
        <tr>
            <th>Client No</th>
            <th>Service</th>
            <th>Priority</th>
            <th>Window</th>
            <th>Status</th>
            <th>Time</th>
        </tr>
        <?php while ($row = $details->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['client_no']; ?></td>
                <td><?php echo $row['service_type']; ?></td>
                <td><?php echo ucfirst($row['priority']); ?></td>
                <td><?php echo $row['window_no']; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
