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
     * 
     * W środowisku lokalnym Nginx sam przekierowuje HTTP->HTTPS (port 80->443).
     * Ta funkcja wymusza HTTPS tylko w produkcji (nie-localhost).
     */
    public static function enforceHttps(): void
    {
        // Sprawdź czy to localhost
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = in_array($host, ['localhost', '127.0.0.1', 'localhost:8080', 'localhost:8443']);
        
        // Dla localhost pomiń - Nginx sam przekieruje HTTP->HTTPS
        if ($isLocal) {
            return;
        }
        
        // W produkcji wymuszaj HTTPS
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        );
        
        if (!$isHttps) {
            $redirectUrl = 'https://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
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
        
        // Sprawdź czy to środowisko lokalne
        $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8080', 'localhost:8443']);
        
        // Konfiguracja bezpiecznej sesji
        ini_set('session.cookie_httponly', '1');  // HttpOnly - ochrona przed XSS
        
        // Secure - tylko HTTPS (wyłączone dla localhost, żeby umożliwić testowanie)
        // W produkcji ZAWSZE włączone (bo nie będzie localhost)
        ini_set('session.cookie_secure', $isLocal ? '0' : '1');
        
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
    /**
     * Konfiguracja wyświetlania błędów (Security Bingo E4)
     * 
     * W produkcji błędy nie powinny być wyświetlane użytkownikowi (display_errors = 0),
     * ale powinny być logowane (log_errors = 1).
     */
    public static function configureErrorDisplay(): void
    {
        // Sprawdź czy to localhost
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = in_array($host, ['localhost', '127.0.0.1', 'localhost:8080', 'localhost:8443']);
        
        if ($isLocal) {
            // Development: Pokaż błędy
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        } else {
            // Production: Ukryj błędy, ale loguj
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
            error_reporting(E_ALL);
            ini_set('log_errors', '1');
            // Opcjonalnie: zdefiniuj ścieżkę do logów jeśli inna niż domyślna
            // ini_set('error_log', '/var/log/php_errors.log');
        }
    }
}
