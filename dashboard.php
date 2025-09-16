<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';
$role = $_SESSION['role'];

// ================== STAFF ACTIONS ==================
if ($role === 'staff' || $role === 'staff') {
    // Call next waiting ticket
    if (isset($_POST['call_next'])) {
        $window = $_POST['window_no'];
        $sql = "SELECT * FROM tickets WHERE status='waiting' ORDER BY id ASC LIMIT 1";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            $ticket = $res->fetch_assoc();
            $update = $conn->prepare("UPDATE tickets SET status='serving', window_no=? WHERE id=?");
            $update->bind_param("si", $window, $ticket['id']);
            $update->execute();
            echo "<script>alert('Now Serving: {$ticket['client_no']} at Window $window');</script>";
        } else {
            echo "<script>alert('No customers in queue');</script>";
        }
    }

    // Mark current as done
    if (isset($_POST['done'])) {
        $window = $_POST['window_no'];
        $sql = "UPDATE tickets SET status='done' WHERE status='serving' AND window_no='$window'";
        $conn->query($sql);
    }
}

// ================== QUEUE SUMMARY ==================
$today = date("Y-m-d");
function countTickets($conn, $status = null, $today) {
    if ($status) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tickets WHERE status=? AND DATE(created_at)=?");
        $stmt->bind_param("ss", $status, $today);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tickets WHERE DATE(created_at)=?");
        $stmt->bind_param("s", $today);
    }
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res['total'] ?? 0;
}

$total_tickets  = countTickets($conn, null, $today);
$waiting_tickets = countTickets($conn, 'waiting', $today);
$serving_tickets = countTickets($conn, 'serving', $today);
$done_tickets    = countTickets($conn, 'done', $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queuing System Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 20px; }
        h1 { color: #2c3e50; }
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .summary { display: flex; gap: 15px; margin-bottom: 20px; }
        .summary-box { flex: 1; padding: 15px; border-radius: 8px; color: white; text-align: center; font-size: 18px; font-weight: bold; }
        .total { background: #3498db; }
        .waiting { background: #e67e22; }
        .serving { background: #27ae60; }
        .done { background: #8e44ad; }
        input, button { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; }
        button { background: #3498db; color: white; border: none; cursor: pointer; }
        button:hover { background: #2980b9; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; font-size: 14px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: center; }
        th { background: #ecf0f1; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo ucfirst($role); ?></h1>
    <p>You are logged in as: <strong><?php echo $_SESSION['username']; ?></strong></p>

    <!-- Queue Summary -->
    <div class="summary">
        <div class="summary-box total">Total: <?php echo $total_tickets; ?></div>
        <div class="summary-box waiting">Waiting: <?php echo $waiting_tickets; ?></div>
        <div class="summary-box serving">Serving: <?php echo $serving_tickets; ?></div>
        <div class="summary-box done">Completed: <?php echo $done_tickets; ?></div>
    </div>

    <!-- Staff Controls -->
    <div class="card">
        <h2>Staff Controls</h2>
        <form method="POST">
            <label>Window Number:</label>
            <input type="text" name="window_no" required>
            <button type="submit" name="call_next">Call Next</button>
            <button type="submit" name="done">Finish Current</button>
        </form>
    </div>

    <!-- Recent Queue -->
    <div class="card">
        <h2>Recent Queue</h2>
        <table>
            <tr>
                <th>Client No</th>
                <th>Service</th>
                <th>Window</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
            <?php
            $res = $conn->query("SELECT * FROM tickets ORDER BY id DESC LIMIT 10");
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['client_no']}</td>
                            <td>{$row['service_type']}</td>
                            <td>{$row['window_no']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['created_at']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No recent tickets found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
