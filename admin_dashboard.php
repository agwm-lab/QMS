<?php
session_start();
include("db.php"); // database connection

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ Only Admin Allowed
if ($_SESSION['role'] !== 'admin') {
    header("Location: staff_dashboard.php"); // redirect staff
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            color: #fff;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            transition: transform 0.3s ease;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 1px solid #34495e;
            padding-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background: #34495e;
        }

        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }

        h1 {
            margin-top: 0;
        }

        section {
            margin-top: 20px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        /* Toggle button */
        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 4px;
            z-index: 1000;
        }

        /* Collapsed sidebar */
        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Toggle button -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin Menu</h2>
        <ul>
            <li><a href="kiosk.php">Kiosk</a></li>
            <li><a href="display.php">Queue Display</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_queue.php">Manage Queue</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <h1>Welcome, Admin</h1>
        <p>You are logged in as: <strong><?php echo $_SESSION['username']; ?></strong></p>

 <!-- Load FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<section>
    <h2 style="margin-bottom: 20px;">Queue Summary (Today)</h2>
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">

        <?php
        $today = date("Y-m-d");

        // Helper function for safe counts
        function getCount($conn, $query) {
            $result = $conn->query($query);
            return $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
        }

        $total_tickets   = getCount($conn, "SELECT COUNT(*) as total FROM tickets WHERE DATE(created_at) = '$today'");
        $serving_tickets = getCount($conn, "SELECT COUNT(*) as total FROM tickets WHERE status='serving' AND DATE(created_at) = '$today'");
        $waiting_tickets = getCount($conn, "SELECT COUNT(*) as total FROM tickets WHERE status='waiting' AND DATE(created_at) = '$today'");
        $done_tickets    = getCount($conn, "SELECT COUNT(*) as total FROM tickets WHERE status='done' AND DATE(created_at) = '$today'");
        ?>

        <!-- Total Tickets -->
        <div style="flex:1; min-width:220px; padding:20px; background:#4e73df; color:white; border-radius:10px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-ticket fa-2x"></i>
            <h3>Total Tickets</h3>
            <p style="font-size: 2em; margin:0;"><?php echo $total_tickets; ?></p>
        </div>

        <!-- Currently Serving -->
        <div style="flex:1; min-width:220px; padding:20px; background:#1cc88a; color:white; border-radius:10px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-bell-concierge fa-2x"></i>
            <h3>Currently Serving</h3>
            <p style="font-size: 2em; margin:0;"><?php echo $serving_tickets; ?></p>
        </div>

        <!-- Waiting -->
        <div style="flex:1; min-width:220px; padding:20px; background:#f6c23e; color:white; border-radius:10px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-hourglass-half fa-2x"></i>
            <h3>Waiting</h3>
            <p style="font-size: 2em; margin:0;"><?php echo $waiting_tickets; ?></p>
        </div>

        <!-- Completed -->
        <div style="flex:1; min-width:220px; padding:20px; background:#e74a3b; color:white; border-radius:10px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-circle-check fa-2x"></i>
            <h3>Completed</h3>
            <p style="font-size: 2em; margin:0;"><?php echo $done_tickets; ?></p>
        </div>
    </div>
</section>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
            document.getElementById("mainContent").classList.toggle("expanded");
        }
    </script>
</body>
</html>
