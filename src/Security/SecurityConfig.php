<?php

namespace Security;

/**
 * SecurityConfig
 * 
 * Centralna konfiguracja bezpieczeństwa aplikacji.
 * Zapewnia wymuszenie HTTPS, bezpieczne sesje i zabezpieczenia przed atakami.
 */
class SecurityConfig
{
    /**
     * Wymusza HTTPS dla całej aplikacji
     * Przekierowuje HTTP na HTTPS jeśli nie jest lokalnym środowiskiem
     */
    public static function enforceHttps(): void
    {
        // Pomiń wymuszenie HTTPS w środowisku lokalnym/dockerze
        $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8080']);
        
        // Sprawdź czy połączenie jest przez HTTP (nie HTTPS)
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        );
        
        // Jeśli nie HTTPS i nie localhost, przekieruj
        if (!$isHttps && !$isLocal) {
            $redirectUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
            header('Location: ' . $redirectUrl, true, 301);
            exit;
        }
    }
    
    /**
     * Inicjalizuje bezpieczną sesję
     * Ustawia wszystkie wymagane flagi bezpieczeństwa
     */
    public static function initSecureSession(): void
    {
        // Zapobiegnij wielokrotnemu startowi sesji
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Konfiguracja bezpiecznej sesji
        ini_set('session.cookie_httponly', '1');  // HttpOnly - ochrona przed XSS
        ini_set('session.cookie_secure', '1');    // Secure - tylko HTTPS
        ini_set('session.cookie_samesite', 'Strict'); // SameSite - ochrona przed CSRF
        ini_set('session.use_strict_mode', '1');  // Strict mode - odrzucanie niezainicjowanych ID
        ini_set('session.use_only_cookies', '1'); // Tylko cookies (nie URL)
        ini_set('session.cookie_lifetime', '0');  // Sesja kończy się po zamknięciu przeglądarki
        
        // Start sesji
        session_start();
        
        // Ustaw timeout sesji (30 minut nieaktywności)
        self::checkSessionTimeout(1800);
    }
    
    /**
     * Sprawdza timeout sesji i wylogowuje jeśli przekroczony
     * 
     * @param int $timeout Czas w sekundach
     */
    private static function checkSessionTimeout(int $timeout): void
    {
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            
            if ($elapsed > $timeout) {
                // Sesja wygasła - wyloguj
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['error'] = 'Sesja wygasła. Zaloguj się ponownie.';
                header('Location: /login');
                exit;
            }
        }
        
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Ustawia nagłówki bezpieczeństwa HTTP
     */
    public static function setSecurityHeaders(): void
    {
        // Zapobieganie clickjacking
        header('X-Frame-Options: DENY');
        
        // Wyłączenie MIME sniffing
        header('X-Content-Type-Options: nosniff');
        
        // XSS Protection (legacy, ale wciąż pomocne)
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Security Policy (podstawowa)
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
