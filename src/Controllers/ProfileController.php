<?php

namespace Controllers;

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

        $user = $_SESSION['user'];
        
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
                'date' => $dateStr,
                'time' => $timeStr,
                'attendees' => 'N/A', // Nie zapisujemy attendees w bazie, placeholder
                'status' => $b['status']
            ];
        }

        /*
         * Dane do statystyk
         * Na razie 'completed' to mock, bo nie mamy historii starszej niż 'upcoming' w tej metodzie
         */
        $stats = [
            'upcoming' => count($upcomingBookings),
            'completed' => 12 // Mock value, w przyszłości dodać metodę getPastBookings
        ];

        require_once __DIR__ . '/../../views/user/profile.php';
    }
}
