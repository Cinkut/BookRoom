<?php

namespace Controllers;

use Repository\UserRepository;
use Security\CsrfProtection;

/**
 * SecurityController
 * 
 * Obsługuje autentykację użytkowników (logowanie, wylogowanie, rejestracja).
 * 
 * Zgodność:
 * - Security Bingo: password_verify(), regenerate_session_id, bezpieczne sesje
 * - RULES.md: Kontroler nie zawiera SQL - używa UserRepository
 */
class SecurityController
{
    private UserRepository $userRepository;
    
    /**
     * Constructor - inicjalizacja repository
     */
    public function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
    }
    
    /**
     * Wyświetl formularz logowania
     */
    public function showLogin(): void
    {
        // Jeśli użytkownik jest już zalogowany, przekieruj do dashboard
        if ($this->isUserLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        
        // Generuj token CSRF dla formularza logowania
        $csrfToken = CsrfProtection::generateToken('login');
        
        require_once __DIR__ . '/../../views/security/login.php';
    }
    
    /**
     * Obsługa logowania (POST)
     * 
     * Weryfikuje dane logowania i tworzy sesję użytkownika.
     * 
     * Security features:
     * - CSRF token validation (zapobieganie atakom CSRF)
     * - Login attempt rate limiting (ochrona przed brute force)
     * - password_verify() dla hashów bcrypt
     * - session_regenerate_id() po zalogowaniu (zapobieganie session fixation)
     * - Walidacja danych wejściowych
     */
    public function login(): void
    {
        // Sprawdź czy żądanie to POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        // Sprawdź czy użytkownik nie jest zablokowany
        if ($this->isLoginBlocked()) {
            $remainingTime = $this->getRemainingBlockTime();
            $_SESSION['error'] = "Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za {$remainingTime} sekund.";
            header('Location: /login');
            exit;
        }
        
        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('login')) {
            $_SESSION['error'] = 'Nieprawidłowy token bezpieczeństwa. Spróbuj ponownie.';
            header('Location: /login');
            exit;
        }
        
        // Pobranie danych z formularza
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Walidacja długości danych wejściowych (ochrona przed buffer overflow)
        if (strlen($email) > 255) {
            $_SESSION['error'] = 'Email jest zbyt długi.';
            header('Location: /login');
            exit;
        }
        
        if (strlen($password) > 128) {
            $_SESSION['error'] = 'Hasło jest zbyt długie.';
            header('Location: /login');
            exit;
        }
        
        // Walidacja podstawowa
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email i hasło są wymagane.';
            header('Location: /login');
            exit;
        }
        
        // Walidacja formatu email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Niepoprawny format adresu email.';
            header('Location: /login');
            exit;
        }
        
        // Pobranie użytkownika z bazy (UserRepository)
        $user = $this->userRepository->findByEmail($email);
        
        // Sprawdzenie czy użytkownik istnieje
        if ($user === null) {
            $this->handleFailedLogin($email);
            $_SESSION['error'] = 'Nieprawidłowy email lub hasło.';
            header('Location: /login');
            exit;
        }
        
        // Weryfikacja hasła (bcrypt hash)
        // Security Bingo: password_verify() zamiast prostego porównania
        if (!password_verify($password, $user['password'])) {
            $this->handleFailedLogin($email);
            $_SESSION['error'] = 'Nieprawidłowy email lub hasło.';
            header('Location: /login');
            exit;
        }
        
        // === LOGOWANIE POMYŚLNE ===
        
        // Wyczyść licznik nieudanych prób
        $this->resetLoginAttempts();
        
        // Security Bingo: Regeneracja ID sesji (zapobieganie session fixation)
        session_regenerate_id(true);
        
        // Zapisanie danych użytkownika w sesji (bez hasła!)
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name']
        ];
        
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Sprawdź czy użytkownik musi zmienić hasło
        if ($user['must_change_password'] === true || $user['must_change_password'] === 't') {
            $_SESSION['info'] = 'Musisz ustawić nowe hasło przed kontynuowaniem.';
            header('Location: /change-password');
            exit;
        }
        
        // Komunikat sukcesu
        $_SESSION['success'] = 'Zalogowano pomyślnie!';
        
        // Przekierowanie do panelu użytkownika
        if ($user['role_name'] === 'admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /dashboard');
        }
        exit;
    }
    
    /**
     * Wylogowanie użytkownika
     * 
     * Security: Niszczenie sesji i regeneracja ID
     */
    public function logout(): void
    {
        // Wyczyść wszystkie tokeny CSRF
        CsrfProtection::clearAllTokens();
        
        // Usunięcie wszystkich zmiennych sesji
        $_SESSION = [];
        
        // Usunięcie cookie sesji
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                '',
                time() - 3600,
                '/',
                '',
                true,  // Secure
                true   // HttpOnly
            );
        }
        
        // Zniszczenie sesji
        session_destroy();
        
        // Rozpoczęcie nowej sesji dla komunikatu
        session_start();
        $_SESSION['success'] = 'Wylogowano pomyślnie.';
        
        // Przekierowanie na stronę logowania
        header('Location: /login');
        exit;
    }
    

    
    /**
     * Sprawdź czy użytkownik jest zalogowany
     * 
     * @return bool True jeśli zalogowany
     */
    private function isUserLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Wymaga zalogowania (middleware)
     * Przekierowuje na login jeśli niezalogowany
     */
    public static function requireLogin(): void
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['error'] = 'Musisz być zalogowany aby zobaczyć tę stronę.';
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Wymaga roli admin
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        
        if (!isset($_SESSION['user']['role_name']) || $_SESSION['user']['role_name'] !== 'admin') {
            $_SESSION['error'] = 'Brak uprawnień do tej strony.';
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Obsługa nieudanej próby logowania
     * 
     * Zwiększa licznik prób, dodaje opóźnienie i blokuje konto po przekroczeniu limitu
     * 
     * @param string $email Email użytkownika (do logowania)
     */
    private function handleFailedLogin(string $email): void
    {
        // Inicjalizacja licznika prób jeśli nie istnieje
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['first_attempt_time'] = time();
        }
        
        // Zwiększ licznik
        $_SESSION['login_attempts']++;
        $attempts = $_SESSION['login_attempts'];
        
        // Logowanie nieudanej próby (bez hasła!)
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        error_log(sprintf(
            "[SECURITY] Failed login attempt #%d for email: %s | IP: %s | User-Agent: %s",
            $attempts,
            $email,
            $ip,
            $userAgent
        ));
        
        // Progresywne opóźnienie (rate limiting)
        // 1-2 próby: 1s, 3-4 próby: 2s, 5+ prób: 3s
        if ($attempts >= 5) {
            sleep(3);
        } elseif ($attempts >= 3) {
            sleep(2);
        } else {
            sleep(1);
        }
        
        // Blokada czasowa po 5 nieudanych próbach
        if ($attempts >= 5) {
            $_SESSION['login_blocked_until'] = time() + 300; // 5 minut blokady
            
            // Logowanie blokady konta
            error_log(sprintf(
                "[SECURITY] Account temporarily blocked for email: %s | IP: %s | Duration: 5 minutes",
                $email,
                $ip
            ));
        }
    }
    
    /**
     * Sprawdza czy logowanie jest zablokowane
     * 
     * @return bool True jeśli zablokowane
     */
    private function isLoginBlocked(): bool
    {
        if (!isset($_SESSION['login_blocked_until'])) {
            return false;
        }
        
        // Sprawdź czy blokada jeszcze trwa
        if (time() < $_SESSION['login_blocked_until']) {
            return true;
        }
        
        // Blokada wygasła - wyczyść
        $this->resetLoginAttempts();
        return false;
    }
    
    /**
     * Zwraca pozostały czas blokady w sekundach
     * 
     * @return int Liczba sekund do końca blokady
     */
    private function getRemainingBlockTime(): int
    {
        if (!isset($_SESSION['login_blocked_until'])) {
            return 0;
        }
        
        $remaining = $_SESSION['login_blocked_until'] - time();
        return max(0, $remaining);
    }
    
    /**
     * Resetuje licznik nieudanych prób logowania
     */
    private function resetLoginAttempts(): void
    {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['first_attempt_time']);
        unset($_SESSION['login_blocked_until']);
    }
}
