<?php

namespace Controllers;

use Repository\RoomRepository;

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
    
    /**
     * Constructor - inicjalizacja repository
     */
    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
    }
    
    /**
     * Lista wszystkich sal konferencyjnych
     * 
     * Pobiera dane z RoomRepository::getAllRooms() (który używa widoku v_room_details)
     * i renderuje widok views/room/index.php
     */
    public function index(): void
    {
        // Pobierz sale z bazy danych (przez Repository)
        $rooms = $this->roomRepository->getAllRooms();
        
        // Renderuj widok
        require_once __DIR__ . '/../../views/room/index.php';
    }
    
    /**
     * Szczegóły pojedynczej sali
     * 
     * @param array $params Parametry URL ['id' => '5']
     */
    public function show(array $params): void
    {
        $roomId = (int)$params['id'];
        
        // Pobierz salę z bazy
        $room = $this->roomRepository->getRoomById($roomId);
        
        if ($room === null) {
            // 404 - sala nie istnieje
            http_response_code(404);
            echo '<h1>404 - Room Not Found</h1>';
            echo '<a href="/rooms">Back to rooms</a>';
            return;
        }
        
        // Renderuj widok szczegółów
        require_once __DIR__ . '/../../views/room/show.php';
    }
}
