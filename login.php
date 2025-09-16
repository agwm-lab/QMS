<?php
session_start();
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); // ⚠️ Use password_hash() in production

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // ✅ Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Queuing System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; }
        .login-box {
            width: 340px; margin: 100px auto;
            padding: 25px; background: white;
            border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        input {
            width: 100%; padding: 12px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 6px;
        }
        button {
            width: 100%; padding: 12px;
            background: #3498db; color: white;
            border: none; border-radius: 6px;
            font-size: 16px; font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #2980b9; }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Staff/Admin Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
