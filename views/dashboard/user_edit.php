<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Panel</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/user-edit.css">
</head>
<body>
    
    <nav class="navbar">
        <div class="nav-brand">
            BookRoom <div class="admin-badge">Admin Panel</div>
        </div>
        <div>
            <a href="/admin/dashboard" style="color: #6B7280; text-decoration: none; font-size: 14px;">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container" style="display: block; max-width: 900px;">
        <div class="card form-card">
            <div class="card-header">
                <h2>Edit User Details</h2>
            </div>
            
            <div class="form-content">
            <form action="/admin/users/<?= $user['id'] ?>/update" method="POST">
                <?php echo Security\CsrfProtection::getTokenField('admin_update_user'); ?>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background-color: #F3F4F6; cursor: not-allowed;">
                    <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Email cannot be changed directly.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" placeholder="e.g. John">
                </div>

                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" placeholder="e.g. Doe">
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select">
                        <option value="2" <?= $user['role_id'] == 2 ? 'selected' : '' ?>>Member</option>
                        <option value="1" <?= $user['role_id'] == 1 ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 12px 24px; font-size: 15px;">Save Changes</button>
                    <a href="/admin/dashboard" class="btn-cancel" style="flex: 1; font-size: 15px;">Cancel</a>
                </div>
            </form>
            </div>
        </div>
    </div>

</body>
</html>
