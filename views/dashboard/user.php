<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - BookRoom</title>
    <style>
        :root {
            --bg-body: #F9FAFB;
            --bg-card: #FFFFFF;
            --text-main: #111827;
            --text-secondary: #6B7280;
            --primary: #1F2937; /* Dark navy/black from screenshot */
            --accent-green-bg: #D1FAE5;
            --accent-green-text: #065F46;
            --accent-red-bg: #FEE2E2;
            --accent-red-text: #991B1B;
            --border: #E5E7EB;
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
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-main);
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            background: #2563EB;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .nav-menu {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .nav-link:hover {
            color: #2563EB;
        }

        /* Main Content */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px;
        }

        /* Search & Filter */
        .controls-section {
            margin-bottom: 32px;
        }

        .search-bar {
            width: 100%;
            max-width: 100%;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
        }
        
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 15px;
            color: var(--text-main);
        }

        .filters {
            display: flex;
            gap: 12px;
        }

        .filter-btn {
            border: 1px solid var(--border);
            background: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            color: var(--text-main);
            transition: all 0.2s;
        }

        .filter-btn.active {
            background: black;
            color: white;
            border-color: black;
        }

        .filter-btn:hover:not(.active) {
            background: #F3F4F6;
        }

        /* Grid */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .room-icon-lg {
            width: 48px;
            height: 48px;
            background: #EFF6FF; /* Default light blue */
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .room-icon-img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .room-info h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .room-capacity {
            color: var(--text-secondary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: var(--accent-green-bg);
            color: var(--accent-green-text);
        }
        
        .status-occupied {
            background: var(--accent-red-bg);
            color: var(--accent-red-text);
        }

        .next-available {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag {
            background: #F3F4F6;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-details {
            background: #000000;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            width: 100%;
            cursor: pointer;
            font-size: 14px;
            margin-top: auto;
            text-decoration: none;
            text-align: center;
            display: block;
            box-sizing: border-box;
        }

        .btn-details:hover {
            background: #1F2937;
        }

    </style>
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
            <a href="#" class="nav-link">
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
                <button class="filter-btn active">All Rooms</button>
                <button class="filter-btn">Available</button>
                <button class="filter-btn">Occupied</button>
            </div>
        </div>

        <!-- Grid -->
        <?php if (empty($rooms)): ?>
            <div style="text-align: center; color: #6B7280; padding: 40px;">
                <p>No rooms found.</p>
            </div>
        <?php else: ?>
            <div class="rooms-grid">
                <?php foreach ($rooms as $room): 
                    // Randomize status for visual demo purposes if not present
                    $isAvailable = true; // Default to available
                    // Use icons based on room name or random
                    $icons = ['🏢', '💡', '👔', '🎨', '🤝', '📚'];
                    $icon = $icons[array_rand($icons)];
                ?>
                <div class="card">
                    <div class="card-header">
                        <div class="room-icon-lg">
                            <span style="font-size: 24px;"><?= $icon ?></span>
                        </div>
                        <div class="room-info">
                            <h3><?= htmlspecialchars($room['name']) ?></h3>
                            <div class="room-capacity">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <?= htmlspecialchars($room['capacity']) ?> people
                            </div>
                        </div>
                    </div>

                    <div>
                        <?php if($isAvailable): ?>
                            <span class="status-badge status-available">Available</span>
                        <?php else: ?>
                            <span class="status-badge status-occupied">Occupied</span>
                        <?php endif; ?>
                    </div>

                    <div class="next-available">
                        Next available: <strong>Now</strong>
                    </div>

                    <div class="tags">
                        <?php 
                        $equipmentItems = !empty($room['equipment_list']) ? explode(', ', $room['equipment_list']) : [];
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

                    <a href="/rooms/<?= $room['id'] ?>" class="btn-details">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>
