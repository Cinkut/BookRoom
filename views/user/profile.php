<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BookRoom</title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 32px;
            margin-top: 24px;
        }

        .profile-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 32px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #EFF6FF;
            color: #2563EB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 600;
            margin: 0 auto 16px;
        }

        .profile-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .profile-email {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 24px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 16px 0;
            margin-bottom: 24px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bookings-section {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 8px 16px;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .tab-btn.active {
            color: var(--text-main);
            border-bottom-color: black;
        }

        .booking-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .booking-item {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            gap: 24px;
            transition: all 0.2s;
        }

        .booking-item:hover {
            border-color: #2563EB;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .date-box {
            background: #F3F4F6;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            min-width: 80px;
        }

        .date-day {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
        }

        .date-month {
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .booking-details {
            flex: 1;
        }

        .booking-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .booking-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-cancel {
            color: #EF4444;
            background: #FEF2F2;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #FEE2E2;
        }

        .empty-state {
            text-align: center;
            padding: 48px;
            color: var(--text-secondary);
        }

        @media(max-width: 900px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/dashboard" class="nav-brand" style="text-decoration: none;">
            <div class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            BookRoom
        </a>
        <div class="nav-menu">
            <a href="/dashboard" class="nav-link">Dashboard</a>
            <a href="/logout" class="nav-link" style="color: #EF4444;">Logout</a>
        </div>
    </nav>

    <div class="container profile-container">
        <!-- Left: Profile Card -->
        <aside class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['email'], 0, 1)) ?>
            </div>
            <h2 class="profile-name">
                <?= htmlspecialchars(explode('@', $user['email'])[0]) ?>
            </h2>
            <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>

            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= $stats['upcoming'] ?></span>
                    <span class="stat-label">Upcoming</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $stats['completed'] ?></span>
                    <span class="stat-label">Completed</span>
                </div>
            </div>

            <div style="text-align: left; margin-top: 24px;">
                <h3 style="font-size: 14px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 12px;">Account Details</h3>
                <div style="font-size: 14px; display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: var(--text-secondary);">Role</span>
                    <span style="font-weight: 500;">
                        <?= ($user['role_id'] == 1) ? 'Administrator' : 'Member' ?>
                    </span>
                </div>
                <div style="font-size: 14px; display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Member Since</span>
                    <span style="font-weight: 500;">
                        <?= isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A' ?>
                    </span>
                </div>
            </div>
        </aside>

        <!-- Right: Bookings -->
        <main class="bookings-section">
            <div class="section-header">
                <div style="font-size: 20px; font-weight: 600;">My Bookings</div>
                <div>
                    <button class="tab-btn active" onclick="switchTab(this, 'upcoming')">Upcoming</button>
                    <button class="tab-btn" onclick="switchTab(this, 'history')">History</button>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <?php
                $successMessages = [
                    'booking_cancelled' => 'Booking cancelled successfully!'
                ];
                $message = $successMessages[$_GET['success']] ?? 'Operation successful!';
                ?>
                <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <?php
                $errorMessages = [
                    'invalid_csrf' => 'Invalid security token. Please try again.',
                    'invalid_booking_id' => 'Invalid booking ID.',
                    'booking_not_found' => 'Booking not found.',
                    'not_your_booking' => 'You can only cancel your own bookings.',
                    'cancel_failed' => 'Failed to cancel booking. It may have already been cancelled.'
                ];
                $message = $errorMessages[$_GET['error']] ?? 'An error occurred: ' . htmlspecialchars($_GET['error']);
                ?>
                <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Upcoming Section -->
            <div id="upcoming-section">
                <?php if (empty($upcomingBookings)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 16px; opacity: 0.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <p>No upcoming bookings found.</p>
                        <a href="/dashboard" class="btn-details" style="display: inline-block; width: auto; padding: 8px 24px; margin-top: 16px;">Book a Room</a>
                    </div>
                <?php else: ?>
                    <div class="booking-list">
                        <?php foreach ($upcomingBookings as $booking): ?>
                        <div class="booking-item">
                            <div class="date-box">
                                <?php 
                                    // Parse date for simplified display
                                    $d = new DateTime($booking['date']); 
                                ?>
                                <div class="date-month"><?= $d->format('M') ?></div>
                                <div class="date-day"><?= $d->format('d') ?></div>
                            </div>
                            <div class="booking-details">
                                <div class="booking-title"><?= htmlspecialchars($booking['title']) ?></div>
                                <div class="booking-meta">
                                    <div class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10 10 10 0 0 0 10-10A10 10 0 0 0 12 2z"></path><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?= htmlspecialchars($booking['time']) ?>
                                    </div>
                                    <div class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4 8 4v14H5z"></path></svg>
                                        <?= htmlspecialchars($booking['room_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="status-badge status-available" style="margin-right: 12px; background: #ECFDF5; color: #047857;">Confirmed</span>
                                
                                <!-- Cancel Button -->
                                <form action="/bookings/cancel" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="csrf_token" value="<?= $booking['csrf_token'] ?>">
                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                    <button type="submit" class="btn-cancel">Cancel</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- History Section -->
            <div id="history-section" style="display: none;">
                <?php if (empty($historyBookings)): ?>
                    <div class="empty-state">
                        <p>No past bookings found.</p>
                    </div>
                <?php else: ?>
                    <div class="booking-list">
                        <?php foreach ($historyBookings as $booking): ?>
                        <div class="booking-item" style="border-color: #E5E7EB; background: #F9FAFB;">
                            <div class="date-box" style="background: #E5E7EB; color: #6B7280;">
                                <?php 
                                    $d = new DateTime($booking['date']); 
                                ?>
                                <div class="date-month"><?= $d->format('M') ?></div>
                                <div class="date-day"><?= $d->format('d') ?></div>
                            </div>
                            <div class="booking-details">
                                <div class="booking-title" style="color: #6B7280;"><?= htmlspecialchars($booking['title']) ?></div>
                                <div class="booking-meta">
                                    <div class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10 10 10 0 0 0 10-10A10 10 0 0 0 12 2z"></path><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?= htmlspecialchars($booking['time']) ?>
                                    </div>
                                    <div class="meta-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4 8 4v14H5z"></path></svg>
                                        <?= htmlspecialchars($booking['room_name']) ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php if($booking['status'] === 'cancelled'): ?>
                                    <span class="status-badge" style="background: #FEF2F2; color: #991B1B;">Cancelled</span>
                                <?php else: ?>
                                    <span class="status-badge" style="background: #F3F4F6; color: #6B7280;">Completed</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <script>
                function switchTab(btn, tabName) {
                    // Update buttons
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // Update sections
                    document.getElementById('upcoming-section').style.display = 'none';
                    document.getElementById('history-section').style.display = 'none';
                    
                    document.getElementById(tabName + '-section').style.display = 'block';
                }
            </script>
        </main>
    </div>

</body>
</html>
