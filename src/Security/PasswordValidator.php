<?php

namespace Security;

/**
 * PasswordValidator
 * 
 * Walidacja złożoności haseł zgodnie z wymogami Security Bingo.
 * Zapewnia że hasła spełniają minimalne wymagania bezpieczeństwa.
 */
class PasswordValidator
{
    /**
     * Minimalna długość hasła
     */
    private const MIN_LENGTH = 8;
    
    /**
     * Maksymalna długość hasła
     */
    private const MAX_LENGTH = 128;
    
    /**
     * Waliduje hasło według kryteriów bezpieczeństwa
     * 
     * Wymagania:
     * - Minimalna długość: 8 znaków
     * - Maksymalna długość: 128 znaków
     * - Przynajmniej jedna cyfra
     * - Przynajmniej jedna wielka litera (opcjonalne, można włączyć)
     * 
     * @param string $password Hasło do sprawdzenia
     * @param bool $requireUppercase Czy wymagać wielkiej litery
     * @return array ['valid' => bool, 'errors' => string[]]
     */
    public static function validate(string $password, bool $requireUppercase = false): array
    {
        $errors = [];
        
        // Sprawdź długość
        $length = mb_strlen($password);
        
        if ($length < self::MIN_LENGTH) {
            $errors[] = sprintf('Hasło musi mieć minimum %d znaków.', self::MIN_LENGTH);
        }
        
        if ($length > self::MAX_LENGTH) {
            $errors[] = sprintf('Hasło może mieć maksymalnie %d znaków.', self::MAX_LENGTH);
        }
        
        // Sprawdź czy zawiera cyfrę
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Hasło musi zawierać przynajmniej jedną cyfrę.';
        }
        
        // Sprawdź wielką literę (opcjonalne)
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Hasło musi zawierać przynajmniej jedną wielką literę.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Zwraca komunikat z wymaganiami dotyczącymi hasła
     * 
     * @param bool $requireUppercase Czy wielkiej litery są wymagane
     * @return string Opis wymagań
     */
    public static function getRequirements(bool $requireUppercase = false): string
    {
        $requirements = [
            sprintf('Minimum %d znaków', self::MIN_LENGTH),
            'Przynajmniej jedna cyfra'
        ];
        
        if ($requireUppercase) {
            $requirements[] = 'Przynajmniej jedna wielka litera';
        }
        
        return implode(', ', $requirements);
    }
}
