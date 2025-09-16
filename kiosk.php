<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Kiosk</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            text-align: center;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            margin: 0;
            padding: 0;
        }
        header {
            background: #198754;
            color: white;
            padding: 20px;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0,0,0,0.2);
        }
        main {
            margin-top: 80px;
        }
        p {
            font-size: 20px;
            margin-bottom: 40px;
            color: #343a40;
        }
        .btn {
            display: inline-block;
            width: 280px;
            padding: 25px;
            margin: 20px;
            font-size: 26px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            border-radius: 12px;
            transition: 0.3s;
            box-shadow: 0px 6px 12px rgba(0,0,0,0.15);
        }
        .btn-regular {
            background: #0d6efd;
        }
        .btn-regular:hover {
            background: #0b5ed7;
        }
        .btn-senior {
            background: #ffc107;
            color: #212529;
        }
        .btn-senior:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>

<header>
    Welcome to the Municipal Treasury
</header>

<main>
    <p>Please select your service type:</p>

    <a href="ticket.php?service=A&priority=Regular" class="btn btn-regular">Regular Customer</a>
    <a href="ticket.php?service=A&priority=Senior/PWD" class="btn btn-senior">Senior / PWD</a>
</main>

</body>
</html>
