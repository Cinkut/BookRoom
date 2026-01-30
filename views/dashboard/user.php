<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>

    <!-- Navbar -->
    <!-- Navbar -->
    <?php include __DIR__ . '/../shared/navbar.php'; ?>

    <main class="container">
        
        <!-- Controls -->
        <div class="controls-section">
            <div class="search-bar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#9CA3AF">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" class="search-input" placeholder="Search conference rooms...">
            </div>

            <div class="filters">
                <button class="filter-btn active" data-filter="all">All Rooms</button>
                <button class="filter-btn" data-filter="available">Available</button>
                <button class="filter-btn" data-filter="occupied">Occupied</button>
            </div>
        </div>

        <!-- Grid -->
        <?php if (empty($rooms)): ?>
            <div style="text-align: center; color: #6B7280; padding: 40px;">
                <p>No rooms found.</p>
            </div>
        <?php else: ?>
            <div class="rooms-grid">
                <?php foreach ($rooms as $roomData): 
                    // Rozpakuj dane
                    $room = $roomData['room'];
                    $isOccupied = $roomData['is_occupied'];
                    $nextAvailable = $roomData['next_available'];
                    
                    // Status
                    $status = $isOccupied ? 'occupied' : 'available';
                    
                    // Formatowanie czasu następnej dostępności
                    $nextAvailableText = 'Now';
                    if ($isOccupied && $nextAvailable) {
                        // Konwertuj z formatu H:i:s na H:i
                        $time = substr($nextAvailable, 0, 5);
                        $nextAvailableText = date('g:i A', strtotime($time));
                    }
                    
                    $icons = ['🏢', '💡', '👔', '🎨', '🤝', '📚'];
                    $icon = $icons[array_rand($icons)];
                ?>
                <div class="room-card" data-status="<?= $status ?>">
                    <div class="card-header">
                        <div class="room-icon-lg">
                            <span style="font-size: 24px;"><?= $icon ?></span>
                        </div>
                        <div class="room-info">
                            <h3><?= htmlspecialchars($room->getName()) ?></h3>
                            <div class="room-capacity">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <?= htmlspecialchars($room->getCapacity()) ?> people
                            </div>
                        </div>
                    </div>

                    <div>
                        <?php if(!$isOccupied): ?>
                            <span class="status-badge status-available">Available</span>
                        <?php else: ?>
                            <span class="status-badge status-occupied">Occupied</span>
                        <?php endif; ?>
                    </div>

                    <div class="next-available">
                        Next available: <strong><?= htmlspecialchars($nextAvailableText) ?></strong>
                    </div>

                    <div class="tags">
                        <?php 
                        $equipmentItems = $room->getEquipment();
                        $displayItems = array_slice($equipmentItems, 0, 2);
                        $remaining = count($equipmentItems) - 2;
                        
                        foreach ($displayItems as $item): 
                            // Simple icon mapping
                            $eqIcon = '📺';
                            if (stripos($item, 'whiteboard') !== false) $eqIcon = '🖊️';
                            if (stripos($item, 'projector') !== false) $eqIcon = '📽️';
                            if (stripos($item, 'audio') !== false) $eqIcon = '🔊';
                        ?>
                            <span class="tag">
                                <?= $eqIcon ?> <?= htmlspecialchars($item) ?>
                            </span>
                        <?php endforeach; ?>
                        
                        <?php if ($remaining > 0): ?>
                            <span class="tag">+<?= $remaining ?> more</span>
                        <?php endif; ?>
                    </div>

                    <a href="/rooms/<?= $room->getId() ?>" class="btn-details">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
