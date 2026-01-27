<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/booking-success.css">
</head>
<body>

    <div class="success-card">
        <div class="icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>Booking Confirmed!</h1>
        <p>Your reservation has been successfully placed. A confirmation email has been sent to you.</p>

        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label">Room:</span>
                <span class="detail-value"><?= htmlspecialchars($roomName) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value"><?= htmlspecialchars($date) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value"><?= htmlspecialchars($timeRange) ?></span>
            </div>
        </div>

        <div class="loader">
            <div class="loader-bar" id="progressBar"></div>
        </div>
        <div class="redirect-text">Redirecting to dashboard...</div>
    </div>

    <script>
        // Animate progress bar
        setTimeout(() => {
            document.getElementById('progressBar').style.width = '100%';
        }, 100);

        // Redirect after 3 seconds
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 3000);
    </script>

</body>
</html>
