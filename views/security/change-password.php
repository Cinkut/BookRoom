<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmień hasło - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/password-change.css">

</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Zmiana hasła</h1>
                <p>Ustaw nowe, bezpieczne hasło</p>
            </div>
            
            <?php if ($isForced): ?>
                <div class="forced-change-notice">
                    <strong>Wymagana zmiana hasła</strong>
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
                
                <!-- Password Strength Meter -->
                <div class="password-strength-container" id="strengthContainer" style="display: none;">
                    <div class="strength-label">
                        <span class="strength-text">Siła hasła:</span>
                        <span class="strength-value" id="strengthText">-</span>
                    </div>
                    <div class="strength-bar-bg">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>
                
                <div class="password-requirements">
                    <h3>Wymagania dotyczące hasła:</h3>
                    <ul id="requirementsList">
                        <li id="req-length" class="requirement">
                            <span class="req-icon">○</span>
                            <span class="req-text">Minimum 8 znaków</span>
                        </li>
                        <li id="req-digit" class="requirement">
                            <span class="req-icon">○</span>
                            <span class="req-text">Przynajmniej jedna cyfra (0-9)</span>
                        </li>
                        <li id="req-lowercase" class="requirement">
                            <span class="req-icon">○</span>
                            <span class="req-text">Przynajmniej jedna mała litera (a-z)</span>
                        </li>
                        <li id="req-uppercase" class="requirement">
                            <span class="req-icon">○</span>
                            <span class="req-text">Przynajmniej jedna wielka litera (A-Z) - opcjonalne</span>
                        </li>
                    </ul>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
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
        
        // Password strength calculation
        function calculatePasswordStrength(password) {
            let strength = 0;
            const checks = {
                length: password.length >= 8,
                digit: /\d/.test(password),
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                special: /[^a-zA-Z0-9]/.test(password)
            };
            
            // Scoring
            if (checks.length) strength += 25;
            if (password.length >= 12) strength += 10;
            if (checks.digit) strength += 20;
            if (checks.lowercase) strength += 20;
            if (checks.uppercase) strength += 15;
            if (checks.special) strength += 10;
            
            return {
                score: Math.min(strength, 100),
                checks: checks
            };
        }
        
        function updateStrengthMeter(password) {
            const container = document.getElementById('strengthContainer');
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            
            if (!password) {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            
            const result = calculatePasswordStrength(password);
            const score = result.score;
            
            // Update bar width
            bar.style.width = score + '%';
            
            // Color and label based on score
            let color, label;
            if (score < 30) {
                color = '#ef4444';
                label = 'Bardzo słabe';
            } else if (score < 50) {
                color = '#f59e0b';
                label = 'Słabe';
            } else if (score < 70) {
                color = '#eab308';
                label = 'Średnie';
            } else if (score < 90) {
                color = '#84cc16';
                label = 'Dobre';
            } else {
                color = '#22c55e';
                label = 'Silne';
            }
            
            bar.style.backgroundColor = color;
            text.textContent = label;
            text.style.color = color;
            
            // Update requirement checkmarks
            updateRequirementChecks(result.checks);
        }
        
        function updateRequirementChecks(checks) {
            const requirements = {
                'req-length': checks.length,
                'req-digit': checks.digit,
                'req-lowercase': checks.lowercase,
                'req-uppercase': checks.uppercase
            };
            
            for (const [id, met] of Object.entries(requirements)) {
                const element = document.getElementById(id);
                const icon = element.querySelector('.req-icon');
                
                if (met) {
                    element.classList.add('met');
                    icon.textContent = '✓';
                } else {
                    element.classList.remove('met');
                    icon.textContent = '○';
                }
            }
        }
        
        // Real-time password strength monitoring
        document.getElementById('new_password').addEventListener('input', function(e) {
            updateStrengthMeter(e.target.value);
        });
    </script>
</body>
</html>
