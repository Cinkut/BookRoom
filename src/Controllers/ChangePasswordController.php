<?php

namespace Controllers;

use Repository\UserRepository;
use Security\CsrfProtection;
use Security\PasswordValidator;

/**
 * ChangePasswordController
 * 
 * Obsługuje zmianę hasła użytkownika (wymuszaną lub dobrowolną)
 */
class ChangePasswordController
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
    }
    
    /**
     * Wyświetl formularz zmiany hasła
     */
    public function showForm(): void
    {
        // Sprawdź czy użytkownik jest zalogowany
        SecurityController::requireLogin();
        
        $csrfToken = CsrfProtection::getTokenField('change_password');
        
        // Sprawdź czy zmiana jest wymuszona
        $isForced = $this->userRepository->mustChangePassword($_SESSION['user']['id']);
        
        require_once __DIR__ . '/../../views/security/change-password.php';
    }
    
    /**
     * Obsługa zmiany hasła (POST)
     */
    public function changePassword(): void
    {
        // Sprawdź czy użytkownik jest zalogowany
        SecurityController::requireLogin();
        
        // Sprawdź czy żądanie to POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /change-password');
            exit;
        }
        
        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('change_password')) {
            $_SESSION['error'] = 'Nieprawidłowy token bezpieczeństwa. Spróbuj ponownie.';
            header('Location: /change-password');
            exit;
        }
        
        // Pobranie danych z formularza
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $userId = $_SESSION['user']['id'];
        
        // Walidacja długości
        if (strlen($newPassword) > 128 || strlen($confirmPassword) > 128) {
            $_SESSION['error'] = 'Hasło jest zbyt długie.';
            header('Location: /change-password');
            exit;
        }
        
        // Walidacja podstawowa
        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'Wszystkie pola są wymagane.';
            header('Location: /change-password');
            exit;
        }
        
        // Sprawdź czy nowe hasła się zgadzają
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Nowe hasła nie są identyczne.';
            header('Location: /change-password');
            exit;
        }
        
        // Tylko jeśli NIE jest wymuszana zmiana, sprawdź stare hasło
        $isForced = $this->userRepository->mustChangePassword($userId);
        
        if (!$isForced && !empty($currentPassword)) {
            // Pobierz użytkownika z bazy
            $user = $this->userRepository->findById($userId);
            
            // Weryfikuj stare hasło
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Obecne hasło jest nieprawidłowe.';
                header('Location: /change-password');
                exit;
            }
            
            // Sprawdź czy nowe hasło nie jest takie samo jak stare
            if (password_verify($newPassword, $user['password'])) {
                $_SESSION['error'] = 'Nowe hasło musi być inne niż obecne.';
                header('Location: /change-password');
                exit;
            }
        }
        
        // Walidacja złożoności nowego hasła
        $passwordValidation = PasswordValidator::validate($newPassword);
        if (!$passwordValidation['valid']) {
            $_SESSION['error'] = implode(' ', $passwordValidation['errors']);
            header('Location: /change-password');
            exit;
        }
        
        // Haszowanie nowego hasła
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Aktualizacja hasła w bazie
        if ($this->userRepository->updatePassword($userId, $passwordHash)) {
            // Wyłącz flagę wymuszającą zmianę hasła
            $this->userRepository->setMustChangePassword($userId, false);
            
            // Komunikat sukcesu
            $_SESSION['success'] = 'Hasło zostało zmienione pomyślnie.';
            
            // Przekierowanie
            if ($_SESSION['user']['role_name'] === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /dashboard');
            }
        } else {
            $_SESSION['error'] = 'Nie udało się zmienić hasła. Spróbuj ponownie.';
            header('Location: /change-password');
        }
        exit;
    }
}
