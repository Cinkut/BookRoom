<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BookRoom</title>
    <style>
        :root {
            --bg-body: #F9FAFB;
            --bg-card: #FFFFFF;
            --text-main: #111827;
            --text-secondary: #6B7280;
            --primary: #2563EB;
            --border: #E5E7EB;
            --success-green: #34D399;
            --pending-yellow: #FBBF24;
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
        }

        /* Layout */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 32px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--border);
            text-align: center;
            height: fit-content;
        }

        .avatar {
            width: 100px;
            height: 100px;
            background: #2563EB;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 600;
            margin: 0 auto 24px;
        }

        .profile-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .profile-role {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .edit-btn {
            background: white;
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
            font-size: 14px;
        }

        .info-list {
            margin-top: 32px;
            text-align: left;
            border-top: 1px solid var(--border);
            padding-top: 24px;
        }

        .info-item {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        .info-icon {
            color: var(--text-secondary);
            width: 20px;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            color: var(--text-main);
        }

        /* Stats */
        .stats-section {
            margin-top: 32px;
            align-items: center;
        }
        
        .stats-section h4 {
            text-align: left;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .stat-box {
            background: #EFF6FF;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .stat-box.completed {
            background: #ECFDF5;
        }

        .stat-num {
            display: block;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .stat-box.completed .stat-num {
            color: #059669;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Bookings Section */
        .bookings-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }

        .tabs {
            display: flex;
            background: #F3F4F6;
            padding: 4px;
            border-radius: 30px;
            margin-bottom: 24px;
            width: fit-content;
        }

        .tab {
            padding: 8px 24px;
            border-radius: 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            border: none;
            background: transparent;
        }

        .tab.active {
            background: white;
            color: var(--text-main);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .booking-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .booking-info h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: var(--text-main);
        }

        .booking-title {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 12px;
            display: block;
        }

        .booking-meta {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: var(--text-secondary);
            align-items: center;
        }

        .booking-meta svg {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            vertical-align: text-top;
        }

        .booking-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
        }

        .status-badge {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 4px;
            background: #D1FAE5;
            color: #065F46;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .status-badge.pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .btn-sm {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-outline {
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        @media (max-width: 768px) {
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
            My Profile
        </a>
    </nav>

    <div class="container">
        <!-- Left Column: Profile Info -->
        <div class="profile-column">
            <div class="profile-card">
                <div class="avatar">
                    <?= strtoupper(substr($user['email'], 0, 2)) ?>
                </div>
                <div class="profile-name">
                    User
                </div>
                <div class="profile-role">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
                
                <button class="edit-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit Profile
                </button>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Phone</div>
                            <div class="info-value">+1 (555) 123-4567</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Department</div>
                            <div class="info-value">Product Management</div>
                        </div>
                    </div>
                </div>

                <div class="stats-section">
                    <h4>Booking Statistics</h4>
                    <div class="stats-grid">
                        <div class="stat-box">
                            <span class="stat-num"><?= $stats['upcoming'] ?></span>
                            <span class="stat-label">Upcoming</span>
                        </div>
                        <div class="stat-box completed">
                            <span class="stat-num"><?= $stats['completed'] ?></span>
                            <span class="stat-label">Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Bookings -->
        <div class="bookings-column">
            <div class="bookings-section">
                <div class="tabs">
                    <button class="tab active">Upcoming Bookings (<?= count($upcomingBookings) ?>)</button>
                    <button class="tab">Past Bookings (<?= $stats['completed'] ?>)</button>
                </div>

                <div class="bookings-list">
                    <?php foreach($upcomingBookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-info">
                            <h3><?= htmlspecialchars($booking['room_name']) ?></h3>
                            <span class="booking-title"><?= htmlspecialchars($booking['title']) ?></span>
                            <div class="booking-meta">
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?= htmlspecialchars($booking['date']) ?>
                                </span>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= htmlspecialchars($booking['time']) ?>
                                </span>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    <?= htmlspecialchars($booking['attendees']) ?> attendees
                                </span>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <span class="status-badge <?= $booking['status'] == 'pending' ? 'pending' : '' ?>">
                                <?= htmlspecialchars($booking['status']) ?>
                            </span>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-sm btn-outline">View Room</button>
                                <button class="btn-sm btn-outline" style="color: #DC2626; border-color: #FECACA;">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
