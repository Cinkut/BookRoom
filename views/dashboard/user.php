<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BookRoom</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #F8F9FA;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            color: #1F2937;
        }
        .user-info {
            color: #6B7280;
        }
        .btn {
            padding: 10px 20px;
            background: #4F7FFF;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn:hover {
            background: #3D6FEF;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard (User)</h1>
                <p class="user-info">
                    Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>
                    (Role: <?= htmlspecialchars($_SESSION['user']['role_name']) ?>)
                </p>
            </div>
            <a href="/logout" class="btn">Logout</a>
        </div>
        
        <div class="card">
            <h2>Your Dashboard</h2>
            <p>This is your user dashboard. Here you can:</p>
            <ul>
                <li>View available rooms (coming soon)</li>
                <li>Book conference rooms (coming soon)</li>
                <li>View your bookings (coming soon)</li>
            </ul>
        </div>
    </div>
</body>
</html>
