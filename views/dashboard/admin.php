<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    
    <?php include __DIR__ . '/../shared/admin_navbar.php'; ?>

    <div class="container">
        
        <!-- Left Col: Users List -->
        <main>
            <?php if (isset($_GET['success'])): ?>
                <?php
                $successMessages = [
                    'user_created' => 'User created successfully!',
                    'user_deleted' => 'User deleted successfully!',
                    'role_updated' => 'User role updated successfully!'
                ];
                $message = $successMessages[$_GET['success']] ?? 'Operation successful!';
                ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <?php
                $errorMessages = [
                    'invalid_csrf' => 'Invalid security token. Please try again.',
                    'missing_fields' => 'All fields are required.',
                    'email_exists' => 'Email already exists.',
                    'invalid_user_id' => 'Invalid user ID.',
                    'cannot_delete_self' => 'You cannot delete your own account.',
                    'cannot_change_own_role' => 'You cannot change your own role.',
                    'user_not_found' => 'User not found.',
                    'delete_failed' => 'Failed to delete user.',
                    'update_failed' => 'Failed to update user role.',
                    'invalid_data' => 'Invalid data provided.'
                ];
                $message = $errorMessages[$_GET['error']] ?? 'An error occurred: ' . htmlspecialchars($_GET['error']);
                ?>
                <div class="alert alert-error"><?= $message ?></div>
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
                            <th>Actions</th>
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
                                <td>
                                    <?php if ($u['id'] !== $_SESSION['user']['id']): ?>
                                        <!-- Edit/Details Button -->
                                        <a href="/admin/users/<?= $u['id'] ?>/edit" class="btn-details">Details</a>
                                        
                                        <!-- Delete User Form -->
                                        <form action="/admin/users/delete" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <?php echo Security\CsrfProtection::getTokenField('admin_delete_user'); ?>
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #9CA3AF; font-size: 12px;">You</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #9CA3AF;">No users found.</td>
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
                        <!-- CSRF Protection -->
                        <?php echo Security\CsrfProtection::getTokenField('admin_create_user'); ?>
                        
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-input" placeholder="e.g. John" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-input" placeholder="e.g. Doe" required>
                        </div>

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
