<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookRoom</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    
    <nav class="navbar">
        <div class="nav-brand">
            BookRoom <div class="admin-badge">Admin Panel</div>
        </div>
        <div>
            <span style="color: #6B7280; font-size: 14px; margin-right: 16px;">
                <?= htmlspecialchars($_SESSION['user']['email']) ?>
            </span>
            <a href="/logout" style="color: #EF4444; text-decoration: none; font-weight: 500; font-size: 14px;">Logout</a>
        </div>
    </nav>

    <div class="container">
        
        <!-- Left Col: Users List -->
        <main>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Operation successful: User created!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">Error: <?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2>System Users</h2>
                </div>
                
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allUsers)): ?>
                            <?php foreach ($allUsers as $u): ?>
                            <tr>
                                <td>#<?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if ($u['role_id'] == 1): ?>
                                        <span class="role-badge role-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="role-badge role-user">Member</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #9CA3AF;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Right Col: Actions -->
        <aside>
            <div class="card">
                <div class="card-header">
                    <h2>Create New User</h2>
                </div>
                <div class="form-content">
                    <form action="/admin/users/create" method="POST">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input" placeholder="user@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role_id" class="form-select">
                                <option value="2">Member</option>
                                <option value="1">Administrator</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary">Create User</button>
                    </form>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <a href="/dashboard" style="display: block; text-align: center; color: #6B7280; text-decoration: none; font-size: 14px;">&larr; Back to Main Dashboard</a>
            </div>
        </aside>

    </div>

</body>
</html>
