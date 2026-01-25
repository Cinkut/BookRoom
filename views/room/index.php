<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Rooms - BookRoom</title>
    <style>
        /* ========================================
           Base Styles
           ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #4F7FFF;
            --primary-hover: #3D6FEF;
            --primary-light: #EBF2FF;
            --success-color: #10B981;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --bg-color: #F8F9FA;
            --card-bg: #FFFFFF;
            --border-color: #E5E7EB;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        /* ========================================
           Container
           ======================================== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* ========================================
           Header
           ======================================== */
        .page-header {
            background: var(--card-bg);
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .page-header .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 4px;
        }
        
        .header-actions {
            display: flex;
            gap: 12px;
        }
        
        /* ========================================
           Buttons
           ======================================== */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        
        .btn-outline {
            background: white;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }
        
        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        /* ========================================
           Room Grid - RESPONSYWNY (RULES.md: Media Queries)
           ======================================== */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }
        
        /* ========================================
           Room Card
           ======================================== */
        .room-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .room-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .room-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .room-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        
        .room-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--primary-color);
        }
        
        .room-name {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        
        .room-capacity {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .room-capacity svg {
            width: 16px;
            height: 16px;
            vertical-align: middle;
            margin-right: 4px;
            fill: currentColor;
        }
        
        .room-body {
            padding: 20px;
        }
        
        .equipment-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        
        .equipment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        
        .equipment-tag {
            background: var(--bg-color);
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            border: 1px solid var(--border-color);
        }
        
        .room-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .availability-badge {
            background: #D1FAE5;
            color: #065F46;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* ========================================
           Empty State
           ======================================== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        
        .empty-state h3 {
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .empty-state p {
            color: var(--text-secondary);
        }
        
        /* ========================================
           MEDIA QUERIES - RESPONSYWNOŚĆ (RULES.md)
           ======================================== */
        
        /* Tablet (768px - 1024px) */
        @media (max-width: 1024px) {
            .rooms-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }
            
            .page-header h1 {
                font-size: 24px;
            }
        }
        
        /* Mobile (do 768px) */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .page-header {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-header h1 {
                font-size: 22px;
            }
            
            .header-actions {
                width: 100%;
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
        
        /* Small Mobile (do 480px) */
        @media (max-width: 480px) {
            .page-header {
                padding: 16px;
            }
            
            .page-header h1 {
                font-size: 20px;
            }
            
            .room-card {
                border-radius: 8px;
            }
            
            .room-name {
                font-size: 18px;
            }
        }
    </style>
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
