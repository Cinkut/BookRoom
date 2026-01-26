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

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
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
        require_once __DIR__ . '/../../views/dashboard/admin.php';
    }
}
