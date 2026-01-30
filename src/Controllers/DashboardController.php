<?php

namespace Controllers;

use Repository\RoomRepository;
use Repository\BookingRepository;
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
    private BookingRepository $bookingRepository;

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
        $this->userRepository = \Repository\UserRepository::getInstance();
        
        // Inicjalizacja BookingRepository
        $db = \Database::getInstance()->getConnection();
        $this->bookingRepository = new BookingRepository($db);
    }

    /**
     * Dashboard użytkownika (wymaga logowania)
     */
    public function index(): void
    {
        // Pobierz listę pokoi
        $rooms = $this->roomRepository->getAllRooms();
        
        // Dla każdego pokoju sprawdź dostępność w czasie rzeczywistym
        $roomsWithAvailability = [];
        foreach ($rooms as $room) {
            $roomId = $room->getId();
            $isOccupied = $this->bookingRepository->isRoomOccupiedNow($roomId);
            $nextAvailable = $this->bookingRepository->getNextAvailableTime($roomId);
            
            $roomsWithAvailability[] = [
                'room' => $room,
                'is_occupied' => $isOccupied,
                'next_available' => $nextAvailable
            ];
        }
        
        // Przekaż dane do widoku
        $rooms = $roomsWithAvailability;
        
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

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 2); // Default to User

        // Walidacja długości email (max 255 znaków)
        if (strlen($email) > 255) {
            $_SESSION['error'] = 'Email jest zbyt długi (max 255 znaków).';
            header('Location: /admin/dashboard');
            exit;
        }

        // Validation
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email i hasło są wymagane.';
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Walidacja formatu email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Niepoprawny format adresu email.';
            header('Location: /admin/dashboard');
            exit;
        }

        // Walidacja złożoności hasła
        $passwordValidation = \Security\PasswordValidator::validate($password);
        if (!$passwordValidation['valid']) {
            $_SESSION['error'] = implode(' ', $passwordValidation['errors']);
            header('Location: /admin/dashboard');
            exit;
        }

        if ($this->userRepository->emailExists($email)) {
            $_SESSION['error'] = 'Użytkownik z tym adresem email już istnieje.';
            header('Location: /admin/dashboard');
            exit;
        }

        // Hashing
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Read names from POST
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        // Create
        $userId = $this->userRepository->create($email, $passwordHash, $roleId, $firstName, $lastName);
        
        if ($userId) {
            // Ustaw flagę wymuszającą zmianę hasła przy pierwszym logowaniu
            $this->userRepository->setMustChangePassword($userId, true);
            
            $_SESSION['success'] = 'Użytkownik został utworzony pomyślnie. Przy pierwszym logowaniu będzie musiał zmienić hasło.';
        } else {
            $_SESSION['error'] = 'Nie udało się utworzyć użytkownika.';
        }

        header('Location: /admin/dashboard');
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
    /**
     * Wyświetl formularz edycji użytkownika
     */
    public function editUser(array $params): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Security check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            header('Location: /dashboard');
            exit;
        }

        $userId = (int)$params['id'];
        $user = $this->userRepository->findById($userId); // Pobiera również dane profilowe

        if (!$user) {
            header('Location: /admin/dashboard?error=user_not_found');
            exit;
        }
        
        require_once __DIR__ . '/../../views/dashboard/user_edit.php';
    }

    /**
     * Aktualizuj dane użytkownika
     */
    public function updateUser(array $params): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Security check
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
            http_response_code(403);
            die('Access Denied');
        }

        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('admin_update_user')) {
             // W przypadku błędu CSRF wróć do formularza edycji, nie dashboardu głównego
            $userId = (int)$params['id'];
            header("Location: /admin/users/$userId/edit?error=invalid_csrf");
            exit;
        }

        $userId = (int)$params['id'];
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);

        // Security: Cannot edit own role
        if ($userId === $_SESSION['user']['id'] && $roleId !== 1) {
            header("Location: /admin/users/$userId/edit?error=cannot_change_own_role");
            exit;
        }

        // 1. Update Profile (Name)
        $this->userRepository->updateProfile($userId, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => '' // Na razie opcjonalne/puste
        ]);

        // 2. Update Role
        if ($this->userRepository->updateRole($userId, $roleId)) {
            header('Location: /admin/dashboard?success=user_updated');
        } else {
            header("Location: /admin/users/$userId/edit?error=update_failed");
        }
    }
}
