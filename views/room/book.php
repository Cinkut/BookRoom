<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($room['name']) ?> - BookRoom</title>
    <style>
        :root {
            --bg-body: #F9FAFB;
            --bg-card: #FFFFFF;
            --text-main: #111827;
            --text-secondary: #6B7280;
            --primary: #1F2937;
            --primary-hover: #111827;
            --border: #E5E7EB;
            --input-bg: #F9FAFB;
            --input-border: #E5E7EB;
            --focus-ring: #E5E7EB; /* Subtle grey ring for now */
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
        }

        .back-link {
            text-decoration: none;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 32px;
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            height: fit-content;
        }

        /* Room Header Mini */
        .room-header-mini {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }

        .room-icon-sm {
            width: 48px;
            height: 48px;
            background: #EFF6FF;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .room-info h2 {
            margin: 0 0 2px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .room-capacity {
            color: var(--text-secondary);
            font-size: 13px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: var(--input-bg);
            border: 1px solid transparent;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            background: white;
            border-color: #D1D5DB;
            box-shadow: 0 0 0 2px rgba(0,0,0,0.05);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            width: 18px;
            height: 18px;
            pointer-events: none;
        }

        .form-control.has-icon {
            padding-left: 42px;
        }

        .capacity-hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 6px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            border: none;
        }

        .btn-primary {
            background: #0F172A; /* Dark navy */
            color: white;
        }

        .btn-primary:hover {
            background: #1E293B;
        }

        .btn-outline {
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #F9FAFB;
        }

        /* Right Column Components */
        .calendar-section h3 {
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 16px 0;
            color: var(--text-secondary);
        }

        /* Calendar Reuse from previous view */
        .calendar-widget {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            background: white;
            margin-bottom: 32px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .nav-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            text-align: center;
            font-size: 13px;
        }
        
        .day-name {
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 8px;
        }

        .day-cell {
            width: 100%;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 6px;
            font-size: 13px;
        }
        
        .day-cell:hover:not(.empty) {
            background: #F3F4F6;
        }

        .day-cell.active {
            background: #0F172A;
            color: white;
        }

        /* Booking Summary */
        .booking-summary {
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .summary-header {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-label {
            color: var(--text-secondary);
        }

        .summary-value {
            font-weight: 500;
            color: var(--text-main);
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
            .form-actions {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/rooms/<?= $room['id'] ?>" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Room Details
        </a>
    </nav>

    <div class="container">
        <!-- Left Column: Form -->
        <div class="card">
            <div class="room-header-mini">
                <div class="room-icon-sm">🏢</div>
                <div class="room-info">
                    <h2><?= htmlspecialchars($room['name']) ?></h2>
                    <div class="room-capacity">Capacity: <?= htmlspecialchars($room['capacity']) ?> people</div>
                </div>
            </div>

            <form action="/rooms/<?= $room['id'] ?>/book" method="POST">
                <!-- CSRF Token (Placeholder) -->
                <input type="hidden" name="csrf_token" value="">

                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <input type="text" class="form-control has-icon" name="date" placeholder="Wednesday, October 29, 2025" value="Wednesday, October 29, 2025">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <input type="text" class="form-control has-icon" name="start_time" value="09:00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <input type="text" class="form-control has-icon" name="end_time" value="10:00">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Number of Attendees</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <input type="number" class="form-control has-icon" name="attendees" placeholder="Enter number of attendees">
                    </div>
                    <div class="capacity-hint">Maximum capacity: <?= htmlspecialchars($room['capacity']) ?> people</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Meeting Purpose</label>
                    <textarea class="form-control" name="purpose" placeholder="Brief description of the meeting..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>

        <!-- Right Column: Context -->
        <div class="right-col">
            <div class="calendar-section">
                <h3>Select Date</h3>
                <div class="calendar-widget">
                    <div class="calendar-header">
                        <div class="nav-btn">&lt;</div>
                        <span>October 2025</span>
                        <div class="nav-btn">&gt;</div>
                    </div>

                    <div class="calendar-grid">
                        <div class="day-name">Su</div>
                        <div class="day-name">Mo</div>
                        <div class="day-name">Tu</div>
                        <div class="day-name">We</div>
                        <div class="day-name">Th</div>
                        <div class="day-name">Fr</div>
                        <div class="day-name">Sa</div>
                        
                        <!-- Mock Days -->
                        <div class="day-cell" style="color: #cbd5e1;">28</div>
                        <div class="day-cell" style="color: #cbd5e1;">29</div>
                        <div class="day-cell" style="color: #cbd5e1;">30</div>
                        <div class="day-cell">1</div>
                        <div class="day-cell">2</div>
                        <div class="day-cell">3</div>
                        <div class="day-cell">4</div>

                        <div class="day-cell">5</div>
                        <div class="day-cell">6</div>
                        <div class="day-cell">7</div>
                        <div class="day-cell">8</div>
                        <div class="day-cell">9</div>
                        <div class="day-cell">10</div>
                        <div class="day-cell">11</div>

                        <div class="day-cell">12</div>
                        <div class="day-cell">13</div>
                        <div class="day-cell">14</div>
                        <div class="day-cell">15</div>
                        <div class="day-cell">16</div>
                        <div class="day-cell">17</div>
                        <div class="day-cell">18</div>

                        <div class="day-cell">19</div>
                        <div class="day-cell">20</div>
                        <div class="day-cell">21</div>
                        <div class="day-cell">22</div>
                        <div class="day-cell">23</div>
                        <div class="day-cell">24</div>
                        <div class="day-cell">25</div>

                        <div class="day-cell">26</div>
                        <div class="day-cell">27</div>
                        <div class="day-cell">28</div>
                        <div class="day-cell active">29</div>
                        <div class="day-cell">30</div>
                        <div class="day-cell">31</div>
                        <div class="day-cell">1</div>
                    </div>
                </div>

                <div class="booking-summary">
                    <div class="summary-header">Booking Summary</div>
                    <div class="summary-row">
                        <span class="summary-label">Date:</span>
                        <span class="summary-value">29.10.2025</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Time:</span>
                        <span class="summary-value">09:00 - 10:00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
