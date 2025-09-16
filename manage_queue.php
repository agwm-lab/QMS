<?php
session_start();
include("db.php");

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ Optional: Only allow admin to manage queue
if ($_SESSION['role'] !== 'admin') {
    header("Location: staff_dashboard.php");
    exit();
}

// ✅ Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tickets WHERE id=$id");
    header("Location: manage_queue.php");
    exit();
}

// ✅ Handle Update (status or window)
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $window_no = intval($_POST['window_no']);
    $stmt = $conn->prepare("UPDATE tickets SET status=?, window_no=? WHERE id=?");
    $stmt->bind_param("sii", $status, $window_no, $id);
    $stmt->execute();
    header("Location: manage_queue.php");
    exit();
}

// ✅ Fetch all tickets
$result = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Queue</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmDelete(ticketNo) {
            return confirm("⚠️ Are you sure you want to delete Ticket #" + ticketNo + "? This action cannot be undone.");
        }
    </script>
</head>
<body>
    <h1>Manage Queue</h1>
    <p><a href="staff_dashboard.php">⬅ Back to Dashboard</a></p>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Client No</th>
            <th>Service Type</th>
            <th>Priority</th>
            <th>Window</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <form method="POST" action="manage_queue.php">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['client_no']); ?></td>
                <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                <td><?php echo htmlspecialchars($row['priority']); ?></td>
                <td>
                    <input type="number" name="window_no" value="<?php echo $row['window_no']; ?>" min="1" style="width:60px;">
                </td>
                <td>
                    <select name="status">
                        <option value="waiting" <?php if($row['status']=="waiting") echo "selected"; ?>>Waiting</option>
                        <option value="serving" <?php if($row['status']=="serving") echo "selected"; ?>>Serving</option>
                        <option value="done" <?php if($row['status']=="done") echo "selected"; ?>>Done</option>
                        <option value="cancelled" <?php if($row['status']=="cancelled") echo "selected"; ?>>Cancelled</option>
                    </select>
                </td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="update">💾 Save</button>
                    <a href="manage_queue.php?delete=<?php echo $row['id']; ?>" 
                       onclick="return confirmDelete('<?php echo $row['id']; ?>')">🗑 Delete</a>
                </td>
            </form>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
