<?php

namespace Repository;

use PDO;
use PDOException;

/**
 * BookingRepository
 * 
 * Odpowiada za operacje na tabeli bookings oraz widokach rezerwacji.
 */
class BookingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tworzy nową rezerwację.
     * 
     * @param int $userId ID użytkownika
     * @param int $roomId ID sali
     * @param string $date Data w formacie Y-m-d
     * @param string $startTime Czas rozpoczęcia H:i
     * @param string $endTime Czas zakończenia H:i
     * @param string $purpose Cel spotkania (opcjonalnie) - na razie nie zapisujemy w bazie, ale w przyszłości można dodać kolumnę
     * @return bool True jeśli się udało
     * @throws PDOException Jeśli wystąpi błąd bazy (np. trigger wykryje konflikt)
     */
    public function createBooking(int $userId, int $roomId, string $date, string $startTime, string $endTime): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO bookings (user_id, room_id, date, start_time, end_time, status)
            VALUES (:user_id, :room_id, :date, :start_time, :end_time, 'confirmed')
        ");

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);

        return $stmt->execute();
    }

    /**
     * Pobiera przyszłe rezerwacje użytkownika.
     * Korzysta z widoku v_upcoming_bookings.
     */
    public function getUpcomingBookingByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM v_upcoming_bookings
            WHERE user_id = :user_id
            ORDER BY date ASC, start_time ASC
        ");
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
