<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - BookRoom</title>
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">
            <div class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            Conference Rooms
        </div>
        <div class="nav-menu">
            <a href="/profile" class="nav-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Profile
            </a>
            <a href="/logout" class="nav-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </a>
        </div>
    </nav>

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

    <script src="/js/dashboard.js"></script>
</body>
</html>
