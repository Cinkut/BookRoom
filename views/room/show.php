<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($room['name']) ?> - Room Details</title>
    <link rel="stylesheet" href="/assets/css/room-details.css">
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
                            <?php if (!empty($isOccupied)): ?>
                                <span class="badge badge-error">Occupied</span>
                                <?php if (!empty($nextAvailable)): ?>
                                    <span class="badge badge-neutral" style="font-size: 11px; margin-left: 4px;">Until <?= substr($nextAvailable, 0, 5) ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-success">Available Now</span>
                            <?php endif; ?>
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
                    <span><!-- JS Fill --></span>
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
                    <!-- JS Fill -->
                </div>
            </div>

            <div class="card">
                <div class="section-title" style="margin-bottom: 12px;">Schedule</div>
                <div class="selected-date-display" style="font-size: 13px; color: #6B7280; margin-bottom: 16px; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px;">
                    <!-- JS Fill -->
                </div>
                
                <div class="schedule-container">
                    <!-- JS Fill -->
                </div>
            </div>
        </div>
    </div>

    <!-- Inject PHP Data for JS -->
    <script>
        window.roomBookings = <?= json_encode($bookings ?? []) ?>;
    </script>
    <script src="/assets/js/room-details.js"></script>
</body>
</html>
