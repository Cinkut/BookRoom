<?php

namespace Controllers;

use Security\CsrfProtection;

/**
 * ProfileController
 * 
 * Obsługuje wyświetlanie profilu użytkownika
 */
class ProfileController
{
    /**
     * Wyświetlanie profilu użytkownika
     */
    private \Repository\BookingRepository $bookingRepository;

    public function __construct()
    {
        $db = \Database::getInstance()->getConnection();
        $this->bookingRepository = new \Repository\BookingRepository($db);
    }

    /**
     * Wyświetlanie profilu użytkownika
     */
    public function index(): void
    {
        // Sprawdź czy użytkownik jest zalogowany
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Pobierz świeże dane użytkownika z bazy, w tym profil
        // Używamy tego z bazy zamiast z sesji (gdzie dane mogą być stare)
        $userRepo = \Repository\UserRepository::getInstance();
        $user = $userRepo->findById($_SESSION['user']['id']);

        // Jeśli użytkownik został usunięty w międzyczasie
        if (!$user) {
            session_destroy();
            header('Location: /login');
            exit;
        }
        
        // Pobierz prawdziwe rezerwacje z bazy
        $dbBookings = $this->bookingRepository->getUpcomingBookingByUser($user['id']);
        
        // Mapowanie danych z bazy na format widoku (żeby nie zmieniać widoku)
        $upcomingBookings = [];
        foreach ($dbBookings as $b) {
            // Formatowanie daty i czasu
            $dateObj = new \DateTime($b['date']);
            $dateStr = $dateObj->format('F j, Y'); // np. October 30, 2025
            
            $startObj = new \DateTime($b['start_time']);
            $endObj = new \DateTime($b['end_time']);
            $timeStr = $startObj->format('g:i A') . ' - ' . $endObj->format('g:i A'); // 2:00 PM - 3:30 PM
            
            $upcomingBookings[] = [
                'id' => $b['booking_id'], // Dodaję ID, przyda się do anulowania/linków
                'room_name' => $b['room_name'],
                'room_id' => $b['room_id'],
                'title' => 'Meeting', // W bazie nie mamy kolumny 'title', dajemy placeholder lub 'Booking'
                'date' => $b['date'], // Oryginalna data dla widoku
                'time' => $timeStr,
                'attendees' => 'N/A', // Nie zapisujemy attendees w bazie, placeholder
                'status' => $b['status'],
                // Generuj unikalny token CSRF dla każdej rezerwacji
                'csrf_token' => CsrfProtection::generateToken('cancel_booking_' . $b['booking_id'])
            ];
        }

        // Pobierz historyczne rezerwacje
        $dbHistory = $this->bookingRepository->getPastBookingsByUser($user['id']);
        $historyBookings = [];
        foreach ($dbHistory as $b) {
            $startObj = new \DateTime($b['start_time']);
            $endObj = new \DateTime($b['end_time']);
            $timeStr = $startObj->format('g:i A') . ' - ' . $endObj->format('g:i A');

            $historyBookings[] = [
                'id' => $b['booking_id'],
                'room_name' => $b['room_name'],
                'room_id' => $b['room_id'],
                'title' => 'Meeting',
                'date' => $b['date'],
                'time' => $timeStr,
                'status' => $b['status']
            ];
        }

        // Generuj token CSRF dla formularza edycji profilu
        CsrfProtection::generateToken('update_profile');

        /*
         * Dane do statystyk
         */
        $stats = [
            'upcoming' => count($upcomingBookings),
            'completed' => count($historyBookings)
        ];

        require_once __DIR__ . '/../../views/user/profile.php';
    }
    
    /**
     * Aktualizacja profilu (telefon)
     */
    public function updateProfile(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Auth check
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        // CSRF check
        if (!CsrfProtection::validateToken('update_profile')) {
            header('Location: /profile?profile_error=invalid_csrf');
            exit;
        }

        $phoneNumber = trim($_POST['phone_number'] ?? '');

        // Walidacja telefonu (dokładnie 9 cyfr)
        if (!preg_match('/^\d{9}$/', $phoneNumber)) {
            header('Location: /profile?profile_error=invalid_phone_format');
            exit;
        }

        // Pobierz aktualne dane, żeby zachować imię i nazwisko (których nie edytujemy tutaj)
        $userRepo = \Repository\UserRepository::getInstance();
        $user = $userRepo->findById($userId);
        $profile = $userRepo->getProfile($userId);

        if (!$profile) {
            // Jeśli z jakiegoś powodu profil nie istnieje (np. stary user), stwórz go?
            // create() w UserRepository tworzy pusty, ale tutaj mamy update.
            // Ale migracja powinna była stworzyć dla wszystkich.
            // Załóżmy, że istnieje.
            header('Location: /profile?profile_error=profile_not_found');
            exit;
        }

        // Przygotuj dane do aktualizacji
        $updateData = [
            'first_name' => $profile['first_name'], // Zachowaj stare
            'last_name' => $profile['last_name'],   // Zachowaj stare
            'phone_number' => $phoneNumber          // Zaktualizuj nowe
        ];

        if ($userRepo->updateProfile($userId, $updateData)) {
            header('Location: /profile?profile_success=1');
        } else {
            header('Location: /profile?profile_error=update_failed');
        }
        exit;
    }
    
    /**
     * Anulowanie rezerwacji
     */
    public function cancelBooking(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Sprawdź czy użytkownik jest zalogowany
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            die('Access Denied');
        }
        
        // Pobierz ID rezerwacji najpierw
        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $userId = $_SESSION['user']['id'];

        // Weryfikacja unikalnego tokena CSRF dla tej konkretnej rezerwacji
        if (!CsrfProtection::validateToken('cancel_booking_' . $bookingId)) {
            header('Location: /profile?error=invalid_csrf');
            exit;
        }
        
        // Walidacja
        if ($bookingId <= 0) {
            header('Location: /profile?error=invalid_booking_id');
            exit;
        }
        
        // Sprawdź czy rezerwacja istnieje i należy do użytkownika
        $booking = $this->bookingRepository->getBookingById($bookingId);
        if (!$booking) {
            header('Location: /profile?error=booking_not_found');
            exit;
        }
        
        if ($booking['user_id'] !== $userId) {
            header('Location: /profile?error=not_your_booking');
            exit;
        }
        
        // Anuluj rezerwację
        if ($this->bookingRepository->cancelBooking($bookingId, $userId)) {
            header('Location: /profile?success=booking_cancelled');
        } else {
            header('Location: /profile?error=cancel_failed');
        }
        exit;
    }
}
