<?php

namespace Controllers;

use Repository\BookingRepository;

/**
 * ApiController
 * 
 * Obsługuje endpointy API (JSON responses)
 */
class ApiController
{
    private BookingRepository $bookingRepository;

    public function __construct()
    {
        $db = \Database::getInstance()->getConnection();
        $this->bookingRepository = new BookingRepository($db);
    }

    /**
     * Pobiera zajęte sloty czasowe dla danej sali i daty
     * 
     * GET /api/rooms/{id}/bookings?date=YYYY-MM-DD
     * 
     * Response: JSON array z zajętymi slotami
     */
    public function getRoomBookings(array $params): void
    {
        header('Content-Type: application/json');
        
        $roomId = (int)($params['id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Walidacja
        if ($roomId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid room ID']);
            return;
        }
        
        // Walidacja daty
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            return;
        }
        
        // Pobierz rezerwacje dla tej sali i daty
        $bookings = $this->bookingRepository->getBookingsForDate($roomId, $date);
        
        // Formatuj do JSON
        $slots = [];
        foreach ($bookings as $booking) {
            $slots[] = [
                'start_time' => substr($booking['start_time'], 0, 5), // HH:MM
                'end_time' => substr($booking['end_time'], 0, 5),     // HH:MM
                'status' => $booking['status']
            ];
        }
        
        echo json_encode([
            'date' => $date,
            'room_id' => $roomId,
            'bookings' => $slots
        ]);
    }
}
