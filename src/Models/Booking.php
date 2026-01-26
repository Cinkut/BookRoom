<?php

namespace Models;

/**
 * Booking Entity
 */
class Booking
{
    private ?int $id;
    private int $userId;
    private int $roomId;
    private string $date;
    private string $startTime;
    private string $endTime;
    private string $status;
    
    // Dodatkowe pola (np. z JOIN)
    private ?string $roomName = null;

    public function __construct(
        ?int $id,
        int $userId,
        int $roomId,
        string $date,
        string $startTime,
        string $endTime,
        string $status = 'confirmed'
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->roomId = $roomId;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->status = $status;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getRoomId(): int { return $this->roomId; }
    public function getDate(): string { return $this->date; }
    public function getStartTime(): string { return $this->startTime; }
    public function getEndTime(): string { return $this->endTime; }
    public function getStatus(): string { return $this->status; }
    public function getRoomName(): ?string { return $this->roomName; }

    public function setRoomName(string $name): void { $this->roomName = $name; }

    public static function fromArray(array $data): self
    {
        $booking = new self(
            $data['booking_id'] ?? $data['id'] ?? null,
            $data['user_id'],
            $data['room_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $data['status'] ?? 'confirmed'
        );

        if (isset($data['room_name'])) {
            $booking->setRoomName($data['room_name']);
        }

        return $booking;
    }
}
