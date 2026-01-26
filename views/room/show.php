<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($room['name']) ?> - Room Details</title>
    <style>
        :root {
            --bg-body: #F9FAFB;
            --bg-card: #FFFFFF;
            --text-main: #111827;
            --text-secondary: #6B7280;
            --primary: #1F2937;
            --accent-blue: #EFF6FF;
            --accent-blue-text: #1D4ED8;
            --border: #E5E7EB;
            --success-green: #D1FAE5;
            --success-text: #065F46;
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
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 24px;
        }

        /* Generic Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        /* Room Header Card */
        .room-header {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
        }

        .room-icon-lg {
            width: 64px;
            height: 64px;
            background: #EFF6FF;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            flex-shrink: 0;
        }

        .room-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 4px 0;
            color: var(--text-main);
        }

        .room-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }

        .badges {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success {
            background: var(--success-green);
            color: var(--success-text);
        }

        .badge-neutral {
            background: #F3F4F6;
            color: var(--text-secondary);
        }

        .room-desc {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn-book {
            background: #000000;
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-book:hover {
            background: #1F2937;
        }

        /* Equipment Section */
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .equipment-item {
            background: #F9FAFB;
            padding: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: var(--text-main);
        }

        .equipment-icon {
            color: #3B82F6;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Amenities Section */
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .amenity-tag {
            background: #F5F3FF;
            color: #7C3AED;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Right Column Components */
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .calendar-nav {
            display: flex;
            gap: 4px;
        }

        .nav-btn {
            border: 1px solid var(--border);
            background: white;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            text-align: center;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .day-name {
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 8px;
        }

        .day-cell {
            padding: 8px 0;
            cursor: pointer;
            border-radius: 6px;
        }
        
        .day-cell:hover:not(.empty) {
            background-color: #F3F4F6;
        }

        .day-cell.active {
            background-color: #000000;
            color: white;
        }

        .schedule-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            background: #F9FAFB;
        }

        .schedule-item.available {
            background: #ECFDF5;
        }

        .schedule-icon {
            margin-top: 2px;
        }
        
        .timeline {
            flex: 1;
        }

        .time-range {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .event-name {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .available .time-range {
            color: #065F46;
        }
        
        .available .event-name {
            color: #059669;
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/dashboard" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Room Details
        </a>
    </nav>

    <div class="container">
        <!-- Room Info Column -->
        <div class="left-col">
            <div class="card">
                <div class="room-header">
                    <div class="room-icon-lg">🏢</div>
                    <div>
                        <h1 class="room-title"><?= htmlspecialchars($room['name']) ?></h1>
                        <div class="room-subtitle">5th Floor, West Wing</div> <!-- Hardcoded location mockup -->
                        <div class="badges">
                            <span class="badge badge-success">Available Now</span>
                            <span class="badge badge-neutral">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Up to <?= htmlspecialchars($room['capacity']) ?> people
                            </span>
                        </div>
                    </div>
                </div>

                <div class="room-desc">
                    <?= htmlspecialchars($room['description'] ?? 'No description available.') ?>
                </div>

                <!-- Mockup button functionality -->
                <a href="/rooms/<?= $room['id'] ?>/book" class="btn-book" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Book This Room</a>
            </div>

            <div class="card">
                <div class="section-title">Equipment & Features</div>
                <div class="equipment-grid">
                    <?php 
                    $equipment = explode(', ', $room['equipment_list'] ?? '');
                    if (empty($room['equipment_list'])) $equipment = ['Standard Meeting Setup'];
                    
                    foreach ($equipment as $item): 
                        // Mock icons based on keywords
                        $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'; // default
                        $itemName = strtolower($item);
                        
                        if (str_contains($itemName, 'wifi')) $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>';
                        if (str_contains($itemName, 'projector')) $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
                        if (str_contains($itemName, 'video')) $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>';
                        if (str_contains($itemName, 'whiteboard')) $icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="22"></line><line x1="15" y1="22" x2="15" y2="22"></line></svg>';
                    ?>
                    <div class="equipment-item">
                        <div class="equipment-icon">
                            <?= $icon ?>
                        </div>
                        <span><?= htmlspecialchars($item) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="section-title">Amenities</div>
                <div class="amenities-list">
                    <!-- Static Mock Data as per screenshot requirements -->
                    <span class="amenity-tag">Coffee Machine</span>
                    <span class="amenity-tag">Water Dispenser</span>
                    <span class="amenity-tag">Climate Control</span>
                    <span class="amenity-tag">Natural Lighting</span>
                </div>
            </div>
        </div>

        <!-- Right Column (Sidebar) -->
        <div class="right-col">
            <div class="card">
                <div class="section-title">Availability Calendar</div>
                <div class="calendar-header">
                    <div class="nav-btn">&lt;</div>
                    <span>January 2026</span>
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
                    
                    <!-- Mock Calendar Days -->
                    <div class="day-cell" style="color: #cbd5e1;">28</div>
                    <div class="day-cell" style="color: #cbd5e1;">29</div>
                    <div class="day-cell" style="color: #cbd5e1;">30</div>
                    <div class="day-cell" style="color: #cbd5e1;">31</div>
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
                    <div class="day-cell active">26</div>
                    <div class="day-cell">27</div>
                    <div class="day-cell">28</div>
                    <div class="day-cell">29</div>
                    <div class="day-cell">30</div>
                    <div class="day-cell">31</div>
                </div>
            </div>

            <div class="card">
                <div class="section-title">Today's Schedule</div>
                
                <div class="schedule-item">
                    <div class="schedule-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#64748b">
                            <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="timeline">
                        <div class="time-range">9:00 AM - 10:30 AM</div>
                        <div class="event-name">Team Standup</div>
                    </div>
                </div>

                <div class="schedule-item available">
                    <div class="schedule-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#059669">
                            <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="timeline">
                        <div class="time-range">10:30 AM - 12:00 PM</div>
                        <div class="event-name">Available</div>
                    </div>
                </div>

                <div class="schedule-item">
                    <div class="schedule-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#64748b">
                            <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="timeline">
                        <div class="time-range">12:00 PM - 1:00 PM</div>
                        <div class="event-name">Client Meeting</div>
                    </div>
                </div>

                <div class="schedule-item available">
                    <div class="schedule-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#059669">
                            <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="timeline">
                        <div class="time-range">1:00 PM - 5:00 PM</div>
                        <div class="event-name">Available</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
