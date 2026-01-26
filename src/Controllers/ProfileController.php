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
    public function index(): void
    {
        // Sprawdź czy użytkownik jest zalogowany
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Mock danych rezerwacji (ponieważ system rezerwacji jeszcze nie istnieje)
        // TODO: Zastąpić pobieraniem z BookingRepository po implementacji rezerwacji
        $upcomingBookings = [
            [
                'room_name' => 'Executive Suite A',
                'title' => 'Q4 Planning Session',
                'date' => 'October 30, 2025',
                'time' => '2:00 PM - 3:30 PM',
                'attendees' => 8,
                'status' => 'confirmed'
            ],
            [
                'room_name' => 'Board Room',
                'title' => 'Executive Review',
                'date' => 'November 2, 2025',
                'time' => '10:00 AM - 12:00 PM',
                'attendees' => 12,
                'status' => 'confirmed'
            ],
            [
                'room_name' => 'Creative Studio',
                'title' => 'Design Brainstorm',
                'date' => 'November 5, 2025',
                'time' => '3:00 PM - 4:00 PM',
                'attendees' => 5,
                'status' => 'pending'
            ]
        ];

        /*
         * Dane do statystyk
         */
        $stats = [
            'upcoming' => count($upcomingBookings),
            'completed' => 3 // Mock value
        ];

        require_once __DIR__ . '/../../views/user/profile.php';
    }
}
