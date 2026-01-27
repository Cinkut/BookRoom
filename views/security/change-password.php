<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmień hasło - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <style>
        .password-requirements {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        .password-requirements h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #1e40af;
            font-weight: 600;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #475569;
        }
        
        .password-requirements li {
            margin: 5px 0;
        }
        
        .alert-info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            color: #1e40af;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .forced-change-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            border-left: 4px solid #f59e0b;
            color: #92400e;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 15px;
            font-weight: 500;
        }
        
        .forced-change-notice strong {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>🔒 Zmiana hasła</h1>
                <p>Ustaw nowe, bezpieczne hasło</p>
            </div>
            
            <?php if ($isForced): ?>
                <div class="forced-change-notice">
                    <strong>⚠️ Wymagana zmiana hasła</strong>
                    Administrator utworzył Twoje konto z tymczasowym hasłem. Ze względów bezpieczeństwa musisz ustawić własne, silne hasło przed kontynuowaniem.
                </div>
            <?php endif; ?>
            
            <!-- Komunikaty -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert-info">
                    <?= htmlspecialchars($_SESSION['info']) ?>
                </div>
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>
            
            <form method="POST" action="/change-password" class="auth-form">
                <?= $csrfToken ?>
                
                <?php if (!$isForced): ?>
                <div class="form-group">
                    <label for="current_password">Obecne hasło</label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           placeholder="Wpisz obecne hasło"
                           required>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="new_password">Nowe hasło</label>
                    <input type="password" 
                           id="new_password" 
                           name="new_password" 
                           placeholder="Wpisz nowe hasło"
                           minlength="8"
                           maxlength="128"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Potwierdź nowe hasło</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           placeholder="Wpisz ponownie nowe hasło"
                           minlength="8"
                           maxlength="128"
                           required>
                </div>
                
                <div class="password-requirements">
                    <h3>📋 Wymagania dotyczące hasła:</h3>
                    <ul>
                        <li>Minimum 8 znaków</li>
                        <li>Przynajmniej jedna cyfra (0-9)</li>
                        <li>Nie używaj łatwego do odgadnięcia hasła</li>
                        <li>Nie używaj tego samego hasła co wcześniej</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn-primary">
                    Zmień hasło
                </button>
                
                <?php if (!$isForced): ?>
                <a href="<?= $_SESSION['user']['role_name'] === 'admin' ? '/admin/dashboard' : '/dashboard' ?>" class="link-secondary">
                    Anuluj
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <script>
        // Client-side validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Hasła nie są identyczne!');
                return false;
            }
            
            // Check minimum requirements
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Hasło musi mieć minimum 8 znaków!');
                return false;
            }
            
            // Check for at least one digit
            if (!/\d/.test(newPassword)) {
                e.preventDefault();
                alert('Hasło musi zawierać przynajmniej jedną cyfrę!');
                return false;
            }
        });
        
        // Show password strength
        document.getElementById('new_password').addEventListener('input', function(e) {
            const password = e.target.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            const colors = ['#ef4444', '#f59e0b', '#eab308', '#84cc16', '#22c55e'];
            const labels = ['Bardzo słabe', 'Słabe', 'Średnie', 'Dobre', 'Silne'];
            
            // Visual feedback (you could add a strength indicator here)
        });
    </script>
</body>
</html>
