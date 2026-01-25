<?php

namespace Controllers;

use Repository\UserRepository;

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
        $this->userRepository = new UserRepository();
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
        
        require_once __DIR__ . '/../../views/security/login.php';
    }
    
    /**
     * Obsługa logowania (POST)
     * 
     * Weryfikuje dane logowania i tworzy sesję użytkownika.
     * 
     * Security features:
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
        
        // Pobranie danych z formularza
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
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
            $_SESSION['error'] = 'Nieprawidłowy email lub hasło.';
            header('Location: /login');
            exit;
        }
        
        // Weryfikacja hasła (bcrypt hash)
        // Security Bingo: password_verify() zamiast prostego porównania
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Nieprawidłowy email lub hasło.';
            header('Location: /login');
            exit;
        }
        
        // === LOGOWANIE POMYŚLNE ===
        
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
     * Wyświetl formularz rejestracji (na przyszłość)
     */
    public function showRegister(): void
    {
        require_once __DIR__ . '/../../views/security/register.php';
    }
    
    /**
     * Obsługa rejestracji (POST)
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Walidacja
        if (empty($email) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = 'Wszystkie pola są wymagane.';
            header('Location: /register');
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Niepoprawny format adresu email.';
            header('Location: /register');
            exit;
        }
        
        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Hasła nie są identyczne.';
            header('Location: /register');
            exit;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Hasło musi mieć minimum 6 znaków.';
            header('Location: /register');
            exit;
        }
        
        // Sprawdzenie czy email już istnieje
        if ($this->userRepository->emailExists($email)) {
            $_SESSION['error'] = 'Ten adres email jest już zarejestrowany.';
            header('Location: /register');
            exit;
        }
        
        // Utworzenie hash hasła (bcrypt)
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Utworzenie użytkownika
        $userId = $this->userRepository->create($email, $passwordHash, 2); // role_id = 2 (user)
        
        if ($userId === null) {
            $_SESSION['error'] = 'Błąd podczas rejestracji. Spróbuj ponownie.';
            header('Location: /register');
            exit;
        }
        
        // Rejestracja pomyślna
        $_SESSION['success'] = 'Rejestracja pomyślna! Możesz się teraz zalogować.';
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
}
