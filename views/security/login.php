<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - BookRoom</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo / Header -->
            <div class="auth-header">
                <h1>BookRoom</h1>
                <p class="subtitle">Zarezerwuj salę konferencyjną</p>
            </div>
            
            <!-- Komunikaty (błąd/sukces) -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✓</span>
                    <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <!-- Formularz logowania -->
            <form method="POST" action="/login" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="np. admin@bookroom.com"
                        required
                        autocomplete="email"
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Wprowadź hasło"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Zaloguj się
                </button>
            </form>
            
            <!-- Link do rejestracji -->
            <div class="auth-footer">
                <p>Nie masz konta? <a href="/register">Zarejestruj się</a></p>
            </div>
            
            <!-- Demo credentials (usuń w produkcji) -->
            <div class="demo-credentials">
                <p><strong>Demo:</strong></p>
                <p>Email: admin@bookroom.com</p>
                <p>Hasło: admin123</p>
            </div>
        </div>
    </div>
</body>
</html>
