<?php

namespace Controllers;

use Repository\RoomRepository;
use Security\CsrfProtection;

/**
 * RoomController
 * 
 * Obsługuje wyświetlanie sal konferencyjnych
 * 
 * Zgodność:
 * - RULES.md: Kontroler nie zawiera SQL - używa RoomRepository
 * - BINGO D1: Separacja logiki - SQL w Repository
 */
class RoomController
{

    private RoomRepository $roomRepository;
    private \Repository\BookingRepository $bookingRepository;

    public function __construct()
    {
        $db = \Database::getInstance()->getConnection();
        $this->roomRepository = new RoomRepository();
        $this->bookingRepository = new \Repository\BookingRepository($db);
    }

    /**
     * Lista pokoi
     */
    public function index(): void
    {
        // Sprawdź czy użytkownik jest zalogowany (middleware auth to robi, ale dla pewności)
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $rooms = $this->roomRepository->getAllRooms();
        $user = $_SESSION['user'];

        // Renderuj widok dashboard
        // TODO: Zmienić na dedykowany widok listy pokoi jeśli powstanie, na razie dashboard
        // W routingu index() jest mapowane na /rooms, a dashboard na DashboardController
        header('Location: /dashboard');
        exit;
    }

    /**
     * Szczegóły pokoju
     */
    public function show(array $params): void
    {
        $id = (int)$params['id'];
        $room = $this->roomRepository->getRoomById($id);

        if ($room === null) {
            http_response_code(404);
            echo "Room not found";
            return;
        }

        // Pobierz kalendarz rezerwacji dla tej sali
        $bookings = $this->bookingRepository->getUpcomingBookingsByRoom($id);
        
        // Sprawdź dostępność w czasie rzeczywistym
        $isOccupied = $this->bookingRepository->isRoomOccupiedNow($id);
        $nextAvailable = null;
        if ($isOccupied) {
            $nextAvailable = $this->bookingRepository->getNextAvailableTime($id);
        }

        // Renderuj widok szczegółów
        require_once __DIR__ . '/../../views/room/show.php';
    }

    /**
     * Formularz rezerwacji
     */
    public function book(array $params): void
    {
        $roomId = (int)$params['id'];
        $room = $this->roomRepository->getRoomById($roomId);
        
        if ($room === null) {
            http_response_code(404);
            echo 'Room not found';
            return;
        }

        // Generuj token CSRF dla formularza rezerwacji
        $csrfToken = CsrfProtection::generateToken('booking');

        require_once __DIR__ . '/../../views/room/book.php';
    }

    /**
     * Przetwarzanie rezerwacji (POST)
     */
    public function processBook(array $params): void
    {
        // Weryfikacja tokena CSRF
        if (!CsrfProtection::validateToken('booking')) {
            http_response_code(403);
            die('Invalid CSRF token. Please try again.');
        }
        
        $roomId = (int)$params['id'];
        $userId = $_SESSION['user']['id'] ?? 0; // Powinno być zawsze ustawione dzięki sesji

        $room = $this->roomRepository->getRoomById($roomId);
        if ($room === null) {
            http_response_code(404);
            die('Room not found');
        }

        // Pobranie danych z formularza
        // TODO: Dodać lepszą walidację danych wejściowych
        $date = $_POST['date'] ?? date('Y-m-d'); // Oczekiwany format YYYY-MM-DD
        $startTime = $_POST['start_time'] ?? '09:00';
        $endTime = $_POST['end_time'] ?? '10:00';
        $attendees = $_POST['attendees'] ?? 1;
        
        // Prosta konwersja daty z formatu "Wednesday, October 29, 2025" na "2025-10-29" jeśli potrzeba
        // Na razie zakładamy, że input date przesyła Y-m-d lub użytkownik wpisał poprawnie
        // W wersji produkcyjnej należałoby to sparsować biblioteką DateTime
        try {
           $dt = new \DateTime($date);
           $formattedDate = $dt->format('Y-m-d');
           
           // Validation: Prevent booking in the past
           $bookingStartDateTime = new \DateTime($formattedDate . ' ' . $startTime);
           $currentDateTime = new \DateTime();

           if ($bookingStartDateTime < $currentDateTime) {
               throw new \Exception("Cannot book a room in the past. Please select a future date and time.");
           }

        } catch (\Exception $e) {
             // Handle invalid date or past date by rendering error
             echo "<div style='padding: 20px; text-align: center; font-family: sans-serif;'>";
             echo "<h2 style='color: #dc2626;'>Validation Error</h2>";
             echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
             echo "<a href='/rooms/$roomId/book' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; border-radius: 6px;'>Go Back</a>";
             echo "</div>";
             return;
        }

        try {
            // Próba zapisu do bazy
            $this->bookingRepository->createBooking(
                $userId, 
                $roomId, 
                $formattedDate, 
                $startTime, 
                $endTime
            );

            // Dane do widoku sukcesu
            $roomName = $room['name'];
            $timeRange = "$startTime - $endTime";
            $date = $formattedDate; // Nadpisujemy zmienną date formatem bazy, żeby ładnie wyglądało

            require_once __DIR__ . '/../../views/room/success.php';

        } catch (\PDOException $e) {
            // Błąd bazy danych (np. trigger konfliktu terminów)
            // W prosty sposób wyświetlamy błąd i przycisk powrotu
            // TODO: W przyszłości przekazać błąd do widoku book.php i wyświetlić w formularzu
            $errorMessage = "Booking failed! The room is likely already booked for this time slot.";
            if (str_contains($e->getMessage(), 'conflict')) {
                 $errorMessage = "This time slot is already occupied.";
            }
            
            echo "<div style='padding: 20px; text-align: center; font-family: sans-serif;'>";
            echo "<h2 style='color: #dc2626;'>Error</h2>";
            echo "<p>$errorMessage</p>";
            echo "<p><small>Technical details: " . htmlspecialchars($e->getMessage()) . "</small></p>";
            echo "<a href='/rooms/$roomId/book' style='display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; border-radius: 6px;'>Try Again</a>";
            echo "</div>";
        }
    }
}
