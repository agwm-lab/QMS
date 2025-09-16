<?php
include 'config.php';
$res = $conn->query("SELECT * FROM tickets WHERE status='serving' ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Display</title>
    <meta http-equiv="refresh" content="5"> <!-- auto refresh -->
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: center;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            margin: 0;
            padding: 0;
        }
        header {
            background: #007bff;
            color: white;
            padding: 20px;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
        }
        table {
            margin: 50px auto;
            border-collapse: collapse;
            width: 70%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 6px 12px rgba(0,0,0,0.1);
        }
        th {
            background: #343a40;
            color: white;
            font-size: 28px;
            padding: 20px;
        }
        td {
            padding: 25px;
            font-size: 40px;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        tr:nth-child(even) {
            background: #f1f3f5;
        }
        tr:hover {
            background: #dbeafe;
            transition: 0.3s;
        }
        .highlight {
            background: #ffc107 !important;
            color: #212529;
        }
    </style>
</head>
<body>
<header>NOW SERVING</header>

<table>
    <tr>
        <th>Client Number</th>
        <th>Window</th>
    </tr>
    <?php 
    $first = true;
    while ($row = $res->fetch_assoc()) { ?>
        <tr class="<?php echo $first ? 'highlight' : ''; ?>">
            <td><?php echo $row['client_no']; ?></td>
            <td>Window <?php echo $row['window_no']; ?></td>
        </tr>
    <?php 
    $first = false;
    } ?>
</table>
</body>
</html>
