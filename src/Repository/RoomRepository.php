<?php

namespace Repository;

use Database;
use PDO;
use PDOException;

/**
 * RoomRepository
 * 
 * Warstwa dostępu do danych dla sal konferencyjnych.
 * Wykorzystuje widok v_room_details z bazy danych (zaawansowany obiekt SQL).
 * 
 * Zgodność:
 * - BINGO D1: Zapytania SQL w warstwie Repository (nie w kontrolerach)
 * - WDPAI: Wykorzystanie widoku v_room_details (STRING_AGG + JOIN)
 * - RULES.md: Separacja concerns, brak duplikacji kodu
 */
class RoomRepository
{
    /**
     * PDO database connection
     */
    private PDO $db;
    
    /**
     * Constructor - inicjalizacja połączenia z bazą
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Pobierz wszystkie sale konferencyjne z wyposażeniem
     * 
     * @return \Models\Room[] Lista obiektów Room
     */
    public function getAllRooms(): array
    {
        try {
            $sql = "SELECT * FROM v_room_details ORDER BY capacity DESC";
            $stmt = $this->db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Mapowanie tablic na obiekty (OOP)
            $rooms = [];
            foreach ($results as $row) {
                $rooms[] = \Models\Room::fromArray($row);
            }
            
            return $rooms;
            
        } catch (PDOException $e) {
            error_log("RoomRepository::getAllRooms - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Pobierz salę po ID z wyposażeniem
     * 
     * @param int $id ID sali
     * @return array|null Dane sali lub null jeśli nie znaleziono
     */
    public function getRoomById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM v_room_details WHERE id = :id LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $room = $stmt->fetch();
            
            return $room ?: null;
            
        } catch (PDOException $e) {
            error_log("RoomRepository::getRoomById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Pobierz dostępne sale (bez rezerwacji w danym przedziale czasowym)
     * 
     * @param string $date Data rezerwacji (format: YYYY-MM-DD)
     * @param string $startTime Godzina rozpoczęcia (format: HH:MM)
     * @param string $endTime Godzina zakończenia (format: HH:MM)
     * @return array Lista dostępnych sal
     */
    public function getAvailableRooms(string $date, string $startTime, string $endTime): array
    {
        try {
            $sql = "
                SELECT r.*
                FROM v_room_details r
                WHERE r.id NOT IN (
                    SELECT room_id
                    FROM bookings
                    WHERE date = :date
                    AND status != 'cancelled'
                    AND (
                        (start_time <= :start_time AND end_time > :start_time)
                        OR (start_time < :end_time AND end_time >= :end_time)
                        OR (start_time >= :start_time AND end_time <= :end_time)
                    )
                )
                ORDER BY r.capacity DESC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("RoomRepository::getAvailableRooms - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Pobierz sale według minimalnej pojemności
     * 
     * @param int $minCapacity Minimalna pojemność
     * @return array Lista sal
     */
    public function getRoomsByCapacity(int $minCapacity): array
    {
        try {
            $sql = "
                SELECT * FROM v_room_details 
                WHERE capacity >= :min_capacity
                ORDER BY capacity ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':min_capacity', $minCapacity, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("RoomRepository::getRoomsByCapacity - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Policz wszystkie sale
     * 
     * @return int Liczba sal
     */
    public function countRooms(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM rooms";
            
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            
            return (int)($result['count'] ?? 0);
            
        } catch (PDOException $e) {
            error_log("RoomRepository::countRooms - Error: " . $e->getMessage());
            return 0;
        }
    }
}
