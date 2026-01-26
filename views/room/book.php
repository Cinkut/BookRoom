<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($room['name']) ?> - BookRoom</title>
    <link rel="stylesheet" href="/css/booking-form.css">
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
