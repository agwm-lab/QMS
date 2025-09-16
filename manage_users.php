<?php
session_start();
include("db.php");

// ✅ Only logged-in admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// ✅ Add User
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php");
    exit();
}

// ✅ Delete User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) { // prevent admin deleting himself
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_users.php");
    exit();
}

// ✅ Fetch All Users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f2f2f2; }
        form { margin-bottom: 20px; }
        input, select, button { padding: 8px; margin: 5px; }
        .delete-btn { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | 
       <a href="admin_dashboard.php">Back to Dashboard</a></p>

    <!-- ✅ Add New User Form -->
    <h2>Add New User</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="add_user">Add User</button>
    </form>

    <!-- ✅ User List -->
    <h2>Existing Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo $row['role']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <a class="delete-btn" href="manage_users.php?delete=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <?php else: ?>
                        (You)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
