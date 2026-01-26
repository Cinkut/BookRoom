<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - BookRoom</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #F9FAFB;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border: 1px solid #E5E7EB;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #D1FAE5;
            color: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .icon-wrapper svg {
            width: 40px;
            height: 40px;
        }

        h1 {
            color: #1F2937;
            font-size: 24px;
            margin: 0 0 12px 0;
        }

        p {
            color: #6B7280;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .booking-details {
            background: #F3F4F6;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: #6B7280;
        }

        .detail-value {
            font-weight: 600;
            color: #1F2937;
        }

        .loader {
            width: 100%;
            height: 4px;
            background: #E5E7EB;
            border-radius: 2px;
            overflow: hidden;
            position: relative;
        }

        .loader-bar {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0%;
            background: #059669;
            transition: width 3s linear;
        }

        .redirect-text {
            font-size: 13px;
            color: #9CA3AF;
            margin-top: 12px;
        }
    </style>
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
