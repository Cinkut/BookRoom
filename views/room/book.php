<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($room['name']) ?> - BookRoom</title>
    <link rel="stylesheet" href="/css/booking-form.css">
    <style>
        /* Fallback if css file is missing or cached */
        .day-cell { cursor: pointer; }
        .day-cell:hover { background-color: #f3f4f6; border-radius: 4px; }
        .day-cell.active { background-color: black; color: white; border-radius: 4px; }
        .booking-summary { margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
        .capacity-hint { font-size: 12px; color: #6b7280; margin-top: 4px; }
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
                    <div class="room-capacity">Max Capacity: <?= htmlspecialchars($room['capacity']) ?> people</div>
                </div>
            </div>

            <form action="/rooms/<?= $room['id'] ?>/book" method="POST">
                <!-- CSRF Protection -->
                <?php echo Security\CsrfProtection::getTokenField('booking'); ?>
                
                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <!-- Native Date Picker + JS Sync -->
                        <input type="date" class="form-control has-icon" name="date" required 
                               value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <!-- Native Time Picker with 15m steps -->
                            <input type="time" class="form-control has-icon" name="start_time" value="09:00" step="900" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <input type="time" class="form-control has-icon" name="end_time" value="10:00" step="900" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Number of Attendees</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <!-- Validated Number Input -->
                        <input type="number" class="form-control has-icon" name="attendees" placeholder="1" 
                               min="1" max="<?= htmlspecialchars($room['capacity']) ?>" required>
                    </div>
                    <div class="capacity-hint">Must be between 1 and <?= htmlspecialchars($room['capacity']) ?></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Meeting Purpose</label>
                    <textarea class="form-control" name="purpose" placeholder="Brief description (optional)"></textarea>
                </div>

                <div class="form-actions">
                    <a href="/rooms/<?= $room['id'] ?>" class="btn btn-outline" style="text-decoration: none; text-align: center;">Cancel</a>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>

        <!-- Right Column: Context -->
        <div class="right-col">
            <div class="calendar-section">
                <h3>Availability</h3>
                <div class="calendar-widget">
                    <div class="calendar-header">
                        <div class="nav-btn">&lt;</div>
                        <span><!-- JS will fill this --></span>
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
                        <!-- JS will render days here -->
                    </div>
                </div>

                <div class="booking-summary">
                    <div class="summary-header">Selected Date</div>
                    <div class="summary-row">
                        <span class="summary-label">Date:</span>
                        <span class="summary-value date-display"><?= date('Y-m-d') ?></span>
                    </div>
                    
                    <!-- Availability Timeline -->
                    <div id="availability-section" style="margin-top: 20px; display: none;">
                        <div class="summary-header" style="margin-bottom: 12px;">Occupied Time Slots</div>
                        <div id="occupied-slots" style="font-size: 13px; color: #6b7280;">
                            <div style="text-align: center; padding: 12px; color: #9ca3af;">
                                Loading...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conflict Warning -->
                    <div id="conflict-warning" style="display: none; background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 6px; margin-top: 16px; font-size: 13px;">
                        ⚠️ <strong>Time Conflict!</strong><br>
                        <span id="conflict-message"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Pass room ID to JavaScript
        window.ROOM_ID = <?= $room['id'] ?>;
    </script>
    <script src="/js/booking.js"></script>
</body>
</html>
