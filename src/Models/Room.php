<?php

namespace Models;

/**
 * Room Entity
 * 
 * Reprezentuje pojedynczą salę konferencyjną.
 * Zgodność z filarami obiektowości: Hermetyzacja danych.
 */
class Room
{
    private ?int $id;
    private string $name;
    private int $capacity;
    private ?string $description;
    private ?string $imagePath;
    private array $equipment = [];

    public function __construct(
        int $id = null, 
        string $name, 
        int $capacity, 
        ?string $description = null, 
        ?string $imagePath = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->capacity = $capacity;
        $this->description = $description;
        $this->imagePath = $imagePath;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCapacity(): int { return $this->capacity; }
    public function getDescription(): ?string { return $this->description; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function getEquipment(): array { return $this->equipment; }

    // Setters / Logic
    public function setEquipment(array $equipment): void {
        $this->equipment = $equipment;
    }
    
    // Metoda pomocnicza do tworzenia z tablicy (np. z DB)
    public static function fromArray(array $data): self
    {
        $room = new self(
            $data['id'] ?? null,
            $data['name'],
            $data['capacity'],
            $data['description'] ?? null,
            $data['image_path'] ?? null
        );
        
        // Obsługa pola equipment_list z widoku v_room_details
        if (isset($data['equipment_list']) && !empty($data['equipment_list'])) {
            $room->setEquipment(explode(', ', $data['equipment_list']));
        }
        
        return $room;
    }
}
