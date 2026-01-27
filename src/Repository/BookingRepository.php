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
     * Anuluje rezerwację
     * 
     * @param int $bookingId ID rezerwacji
     * @param int $userId ID użytkownika (dla weryfikacji właściciela)
     * @return bool True jeśli anulowanie powiodło się
     */
    public function cancelBooking(int $bookingId, int $userId): bool
    {
        // Sprawdź czy użytkownik jest właścicielem rezerwacji
        $stmt = $this->pdo->prepare("
            UPDATE bookings
            SET status = 'cancelled'
            WHERE id = :booking_id
              AND user_id = :user_id
              AND status = 'confirmed'
        ");
        
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
    
    /**
     * Pobiera szczegóły rezerwacji po ID
     * 
     * @param int $bookingId ID rezerwacji
     * @return array|null Dane rezerwacji lub null
     */
    public function getBookingById(int $bookingId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                b.id,
                b.user_id,
                b.room_id,
                b.date,
                b.start_time,
                b.end_time,
                b.status,
                b.created_at,
                r.name as room_name,
                u.email as user_email
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = :booking_id
        ");
        
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
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
    public function getUpcomingBookingsByRoom(int $roomId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT date, start_time, end_time, status 
            FROM bookings
            WHERE room_id = :room_id 
              AND date >= CURRENT_DATE 
              AND status != 'cancelled'
            ORDER BY date ASC, start_time ASC
        ");
        
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Sprawdza czy sala jest zajęta w bieżącej chwili
     * 
     * @param int $roomId ID sali
     * @return bool True jeśli sala jest zajęta
     */
    public function isRoomOccupiedNow(int $roomId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM bookings
            WHERE room_id = :room_id
              AND date = CURRENT_DATE
              AND start_time <= CURRENT_TIME
              AND end_time > CURRENT_TIME
              AND status = 'confirmed'
        ");
        
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Pobiera czas następnej dostępności sali
     * 
     * @param int $roomId ID sali
     * @return string|null Czas w formacie H:i lub null jeśli dostępna teraz
     */
    public function getNextAvailableTime(int $roomId): ?string
    {
        // Jeśli sala jest wolna teraz, zwróć null
        if (!$this->isRoomOccupiedNow($roomId)) {
            return null;
        }
        
        // Znajdź koniec bieżącej rezerwacji
        $stmt = $this->pdo->prepare("
            SELECT end_time
            FROM bookings
            WHERE room_id = :room_id
              AND date = CURRENT_DATE
              AND start_time <= CURRENT_TIME
              AND end_time > CURRENT_TIME
              AND status = 'confirmed'
            ORDER BY end_time ASC
            LIMIT 1
        ");
        
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['end_time'] ?? null;
    }
    
    /**
     * Pobiera wszystkie rezerwacje dla danej sali i daty
     * 
     * @param int $roomId ID sali
     * @param string $date Data w formacie Y-m-d
     * @return array Lista rezerwacji
     */
    public function getBookingsForDate(int $roomId, string $date): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                id,
                start_time,
                end_time,
                status
            FROM bookings
            WHERE room_id = :room_id
              AND date = :date
              AND status = 'confirmed'
            ORDER BY start_time ASC
        ");
        
        $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Pobiera historyczne rezerwacje użytkownika (przeszłe i anulowane)
     * 
     * @param int $userId ID użytkownika
     * @return array Lista rezerwacji
     */
    public function getPastBookingsByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                b.id as booking_id,
                b.room_id,
                r.name as room_name,
                b.date,
                b.start_time,
                b.end_time,
                b.status
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.user_id = :user_id
              AND (
                  (b.date < CURRENT_DATE) 
                  OR (b.date = CURRENT_DATE AND b.end_time < CURRENT_TIME)
                  OR (b.status = 'cancelled')
              )
            ORDER BY b.date DESC, b.start_time DESC
        ");
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
