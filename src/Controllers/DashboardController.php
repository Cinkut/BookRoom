<?php

namespace Controllers;

use Repository\RoomRepository;

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
}
