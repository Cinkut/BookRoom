<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/profile.css">
</head>
</head>
<body>

    <nav class="navbar">
        <a href="/dashboard" class="nav-brand nav-brand-link">
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
            <a href="/logout" class="nav-link nav-link-logout">Logout</a>
        </div>
    </nav>

    <div class="container profile-container">
        <!-- Left: Profile Card -->
        <aside class="profile-card">
            <div class="profile-avatar">
                <?php if (!empty($user['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="avatar-img">
                <?php else: ?>
                    <?= strtoupper(substr($user['first_name'] ?? $user['email'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <h2 class="profile-name">
                <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: htmlspecialchars(explode('@', $user['email'])[0]) ?>
            </h2>
            <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
            
            <?php if (!empty($user['phone_number'])): ?>
                <div class="profile-phone text-secondary font-sm mt-4">
                    <?= htmlspecialchars($user['phone_number']) ?>
                </div>
            <?php endif; ?>

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

            <div class="text-left mt-24">
                <h3 class="section-title">Account Details</h3>
                <div class="font-sm flex-between mb-8">
                    <span class="text-secondary">Role</span>
                    <span class="font-weight-500">
                        <?= ($user['role_id'] == 1) ? 'Administrator' : 'Member' ?>
                    </span>
                </div>
                <div class="font-sm flex-between mb-8">
                    <span class="text-secondary">Member Since</span>
                    <span class="font-weight-500">
                        <?= isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A' ?>
                    </span>
                </div>

                <!-- Update Profile Form -->
                <form action="/profile/update" method="POST" class="mt-16 pt-16 border-top">
                    <?php echo Security\CsrfProtection::getTokenField('update_profile'); ?>
                    
                    <div class="form-group mb-8">
                        <label class="font-xs text-secondary block mb-4">Phone Number</label>
                        <input type="tel" name="phone_number" class="form-input-sm full-width" 
                               value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" 
                               placeholder="123456789"
                               pattern="\d{9}"
                               maxlength="9"
                               title="Numer telefonu musi składać się z 9 cyfr"
                               required>
                    </div>

                    <!-- Hidden fields to preserve name (if we don't want to show inputs for them) or show them as readonly/editable -->
                    <!-- The user requirement says admin sets name, user sets phone. 
                         We will send current names as hidden to preserve them if we don't want to make them editable here, 
                         OR better: make them editable but maybe readonly? 
                         Let's just make them hidden for now to strictly follow "user fills phone".
                         Actually, to be safe, let's fetch them in controller so we don't rely on hidden fields that can be manipulated.
                    -->
                    
                    <button type="submit" class="btn-update">Update Contact Info</button>
                    
                    <?php if (isset($_GET['profile_success'])): ?>
                        <div class="font-xs text-success mt-8 text-center">Updated successfully!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['profile_error'])): ?>
                        <div class="font-xs text-error mt-8 text-center">Update failed.</div>
                    <?php endif; ?>
                </form>
            </div>
        </aside>

        <!-- Right: Bookings -->
        <main class="bookings-section">
            <div class="section-header">
                <div class="my-bookings-title">My Bookings</div>
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
                <div class="alert-success">
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
                <div class="alert-error">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Upcoming Section -->
            <div id="upcoming-section">
                <?php if (empty($upcomingBookings)): ?>
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon-semitransparent">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <p>No upcoming bookings found.</p>
                        <a href="/dashboard" class="btn-details">Book a Room</a>
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
                                <span class="status-badge status-available mr-12">Confirmed</span>
                                
                                <!-- Cancel Button -->
                                <form action="/bookings/cancel" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
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
            <div id="history-section" class="display-none">
                <?php if (empty($historyBookings)): ?>
                    <div class="empty-state">
                        <p>No past bookings found.</p>
                    </div>
                <?php else: ?>
                    <div class="booking-list">
                        <?php foreach ($historyBookings as $booking): ?>
                        <div class="booking-item booking-item-history">
                            <div class="date-box date-box-history">
                                <?php 
                                    $d = new DateTime($booking['date']); 
                                ?>
                                <div class="date-month"><?= $d->format('M') ?></div>
                                <div class="date-day"><?= $d->format('d') ?></div>
                            </div>
                            <div class="booking-details">
                                <div class="booking-title booking-title-history"><?= htmlspecialchars($booking['title']) ?></div>
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
                                    <span class="status-badge status-cancelled">Cancelled</span>
                                <?php else: ?>
                                    <span class="status-badge status-completed">Completed</span>
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
                    document.getElementById('upcoming-section').classList.add('display-none');
                    document.getElementById('history-section').classList.add('display-none');
                    
                    document.getElementById(tabName + '-section').classList.remove('display-none');
                }
            </script>
        </main>
    </div>

</body>
</html>
