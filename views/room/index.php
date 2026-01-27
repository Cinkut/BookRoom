<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/room.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1>Conference Rooms</h1>
                <p class="subtitle">Browse and book available conference rooms</p>
            </div>
            <div class="header-actions">
                <a href="/dashboard" class="btn btn-outline">← Back to Dashboard</a>
                <a href="/logout" class="btn btn-primary">Logout</a>
            </div>
        </div>
        
        <!-- Rooms Grid -->
        <?php if (!empty($rooms)): ?>
            <div class="rooms-grid">
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <!-- Room Header -->
                        <div class="room-header">
                            <div class="room-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"/>
                                </svg>
                            </div>
                            <h2 class="room-name"><?= htmlspecialchars($room['name']) ?></h2>
                            <p class="room-capacity">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                                Up to <?= htmlspecialchars($room['capacity']) ?> people
                            </p>
                        </div>
                        
                        <!-- Room Body - Equipment -->
                        <div class="room-body">
                            <div class="equipment-label">Equipment (<?= htmlspecialchars($room['equipment_count']) ?>)</div>
                            <div class="equipment-list">
                                <?php 
                                $equipmentItems = explode(', ', $room['equipment_list']);
                                foreach ($equipmentItems as $item): 
                                ?>
                                    <span class="equipment-tag"><?= htmlspecialchars($item) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Room Footer -->
                        <div class="room-footer">
                            <span class="availability-badge">Available Now</span>
                            <a href="/rooms/<?= $room['id'] ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <h3>No rooms available</h3>
                <p>There are currently no conference rooms in the system.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
