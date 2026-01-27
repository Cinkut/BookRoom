<?php

namespace Controllers;

use Repository\RoomRepository;
use Security\CsrfProtection;

/**
 * DashboardController
 * 
 * Obsługuje dashboard użytkownika i admina
 */
class DashboardController
{
    private RoomRepository $roomRepository;
    private \Repository\UserRepository $userRepository;

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
        $this->userRepository = new \Repository\UserRepository();
    }

    /**
     * Dashboard użytkownika (wymaga logowania)
     */
    public function index(): void
    {
        // Pobierz listę pokoi, aby wyświetlić je na dashboardzie
        $rooms = $this->roomRepository->getAllRooms();
        
        require_once __DIR__ . '/../../views/dashboard/user.php';
    }
    
    /**
     * Dashboard admina (wymaga roli admin)
     */
    public function admin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Security check: Must be Admin (role_id = 1)
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            header('Location: /dashboard'); // Kick non-admins out
            exit;
        }

        // Generuj tokeny CSRF dla wszystkich formularzy
        $csrfTokenCreate = CsrfProtection::generateToken('admin_create_user');
        $csrfTokenDelete = CsrfProtection::generateToken('admin_delete_user');
        $csrfTokenUpdateRole = CsrfProtection::generateToken('admin_update_role');

        $allUsers = $this->userRepository->findAll();
        require_once __DIR__ . '/../../views/dashboard/admin.php';
    }

    /**
     * Tworzenie nowego użytkownika przez Admina
     */
    public function createUser(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Security check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            http_response_code(403);
            die('Access Denied');
        }

        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('admin_create_user')) {
            header('Location: /admin/dashboard?error=invalid_csrf');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2); // Default to User

        // Validation
        if (empty($email) || empty($password)) {
            // TODO: Error message handling
            header('Location: /admin/dashboard?error=missing_fields');
            exit;
        }

        if ($this->userRepository->emailExists($email)) {
            header('Location: /admin/dashboard?error=email_exists');
            exit;
        }

        // Hashing
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Create
        $this->userRepository->create($email, $passwordHash, $roleId);

        header('Location: /admin/dashboard?success=user_created');
        exit;
    }
    
    /**
     * Usuwanie użytkownika przez Admina
     */
    public function deleteUser(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Security check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            http_response_code(403);
            die('Access Denied');
        }
        
        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('admin_delete_user')) {
            header('Location: /admin/dashboard?error=invalid_csrf');
            exit;
        }
        
        $userId = (int)($_POST['user_id'] ?? 0);
        
        // Walidacja
        if ($userId <= 0) {
            header('Location: /admin/dashboard?error=invalid_user_id');
            exit;
        }
        
        // Zabezpieczenie - nie można usunąć samego siebie
        if ($userId === $_SESSION['user']['id']) {
            header('Location: /admin/dashboard?error=cannot_delete_self');
            exit;
        }
        
        // Sprawdź czy użytkownik istnieje
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            header('Location: /admin/dashboard?error=user_not_found');
            exit;
        }
        
        // Usuń użytkownika
        if ($this->userRepository->delete($userId)) {
            header('Location: /admin/dashboard?success=user_deleted');
        } else {
            header('Location: /admin/dashboard?error=delete_failed');
        }
        exit;
    }
    
    /**
     * Aktualizacja roli użytkownika przez Admina
     */
    public function updateUserRole(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Security check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            http_response_code(403);
            die('Access Denied');
        }
        
        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('admin_update_role')) {
            header('Location: /admin/dashboard?error=invalid_csrf');
            exit;
        }
        
        $userId = (int)($_POST['user_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);
        
        // Walidacja
        if ($userId <= 0 || !in_array($roleId, [1, 2])) {
            header('Location: /admin/dashboard?error=invalid_data');
            exit;
        }
        
        // Zabezpieczenie - nie można zmienić własnej roli
        if ($userId === $_SESSION['user']['id']) {
            header('Location: /admin/dashboard?error=cannot_change_own_role');
            exit;
        }
        
        // Sprawdź czy użytkownik istnieje
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            header('Location: /admin/dashboard?error=user_not_found');
            exit;
        }
        
        // Aktualizuj rolę
        if ($this->userRepository->updateRole($userId, $roleId)) {
            header('Location: /admin/dashboard?success=role_updated');
        } else {
            header('Location: /admin/dashboard?error=update_failed');
        }
        exit;
    }
}
