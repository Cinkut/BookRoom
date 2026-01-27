<?php

namespace Security;

/**
 * CsrfProtection
 * 
 * Klasa do generowania i weryfikacji tokenów CSRF (Cross-Site Request Forgery).
 * 
 * Security features:
 * - Generowanie losowych tokenów dla każdego formularza
 * - Przechowywanie tokenów w sesji
 * - Weryfikacja tokenów przy odbiorze danych POST
 * - Automatyczne usuwanie zużytych tokenów
 */
class CsrfProtection
{
    /**
     * Nazwa klucza w sesji dla tokenów CSRF
     */
    private const SESSION_KEY = 'csrf_tokens';
    
    /**
     * Maksymalny czas życia tokena (w sekundach)
     * Domyślnie: 1 godzina
     */
    private const TOKEN_LIFETIME = 3600;
    
    /**
     * Maksymalna liczba tokenów przechowywanych w sesji
     * Zapobiega przepełnieniu sesji
     */
    private const MAX_TOKENS = 10;
    
    /**
     * Generuje nowy token CSRF i zapisuje go w sesji
     * 
     * @param string $formName Nazwa formularza (np. 'login', 'booking')
     * @return string Wygenerowany token
     */
    public static function generateToken(string $formName = 'default'): string
    {
        // Upewnij się, że sesja jest rozpoczęta
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicjalizacja tablicy tokenów jeśli nie istnieje
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
        
        // Wyczyszczenie starych tokenów
        self::cleanupExpiredTokens();
        
        // Generowanie losowego tokena (32 bajty = 64 znaki hex)
        $token = bin2hex(random_bytes(32));
        
        // Zapisanie tokena w sesji z timestampem
        $_SESSION[self::SESSION_KEY][$formName] = [
            'token' => $token,
            'timestamp' => time()
        ];
        
        // Ograniczenie liczby tokenów w sesji
        self::limitTokenCount();
        
        return $token;
    }
    
    /**
     * Weryfikuje token CSRF z żądania POST
     * 
     * @param string $formName Nazwa formularza
     * @param bool $removeAfterValidation Czy usunąć token po weryfikacji (domyślnie: true)
     * @return bool True jeśli token jest poprawny
     */
    public static function validateToken(string $formName = 'default', bool $removeAfterValidation = true): bool
    {
        // Sprawdź czy sesja jest rozpoczęta
        if (session_status() === PHP_SESSION_NONE) {
            return false;
        }
        
        // Sprawdź czy token został przesłany w POST
        $submittedToken = $_POST['csrf_token'] ?? '';
        
        if (empty($submittedToken)) {
            return false;
        }
        
        // Sprawdź czy token istnieje w sesji
        if (!isset($_SESSION[self::SESSION_KEY][$formName])) {
            return false;
        }
        
        $storedData = $_SESSION[self::SESSION_KEY][$formName];
        $storedToken = $storedData['token'] ?? '';
        $timestamp = $storedData['timestamp'] ?? 0;
        
        // Sprawdź czy token nie wygasł
        if (time() - $timestamp > self::TOKEN_LIFETIME) {
            // Usuń wygasły token
            unset($_SESSION[self::SESSION_KEY][$formName]);
            return false;
        }
        
        // Porównaj tokeny (timing-safe comparison)
        $isValid = hash_equals($storedToken, $submittedToken);
        
        // Usuń token po użyciu (zapobiega ponownemu użyciu)
        if ($isValid && $removeAfterValidation) {
            unset($_SESSION[self::SESSION_KEY][$formName]);
        }
        
        return $isValid;
    }
    
    /**
     * Generuje pole HTML z tokenem CSRF
     * 
     * @param string $formName Nazwa formularza
     * @return string HTML input field
     */
    public static function getTokenField(string $formName = 'default'): string
    {
        $token = self::generateToken($formName);
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Usuwa wygasłe tokeny z sesji
     */
    private static function cleanupExpiredTokens(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return;
        }
        
        $currentTime = time();
        
        foreach ($_SESSION[self::SESSION_KEY] as $formName => $data) {
            $timestamp = $data['timestamp'] ?? 0;
            
            if ($currentTime - $timestamp > self::TOKEN_LIFETIME) {
                unset($_SESSION[self::SESSION_KEY][$formName]);
            }
        }
    }
    
    /**
     * Ogranicza liczbę tokenów w sesji
     * Usuwa najstarsze tokeny jeśli przekroczono limit
     */
    private static function limitTokenCount(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return;
        }
        
        $tokens = $_SESSION[self::SESSION_KEY];
        
        if (count($tokens) <= self::MAX_TOKENS) {
            return;
        }
        
        // Sortuj tokeny według timestampu (najstarsze pierwsze)
        uasort($tokens, function($a, $b) {
            return ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0);
        });
        
        // Usuń najstarsze tokeny
        $tokensToRemove = count($tokens) - self::MAX_TOKENS;
        $removed = 0;
        
        foreach ($tokens as $formName => $data) {
            if ($removed >= $tokensToRemove) {
                break;
            }
            
            unset($_SESSION[self::SESSION_KEY][$formName]);
            $removed++;
        }
    }
    
    /**
     * Usuwa wszystkie tokeny CSRF z sesji
     * Użyteczne przy wylogowaniu
     */
    public static function clearAllTokens(): void
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            unset($_SESSION[self::SESSION_KEY]);
        }
    }
}
