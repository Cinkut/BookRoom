<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookRoom - Environment Test</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>BookRoom Environment is Ready!</h1>
        <p>Docker, PHP and Nginx are working.</p>
        <div class="info">
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Server:</strong> Nginx with PHP-FPM</p>
            <p><strong>Database:</strong> PostgreSQL 15</p>
        </div>
    </div>
</body>
</html>
