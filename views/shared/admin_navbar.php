    <nav class="navbar">
        <div class="nav-brand">
            BookRoom <div class="admin-badge">Admin Panel</div>
        </div>
        <div>
            <span style="color: #6B7280; font-size: 14px; margin-right: 16px;">
                <?= htmlspecialchars($_SESSION['user']['email'] ?? 'Admin') ?>
            </span>
            <a href="/logout" style="color: #EF4444; text-decoration: none; font-weight: 500; font-size: 14px;">Logout</a>
        </div>
    </nav>
