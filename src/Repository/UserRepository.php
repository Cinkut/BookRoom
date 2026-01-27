<?php

namespace Repository;

use Database;
use PDO;
use PDOException;

/**
 * UserRepository
 * 
 * Warstwa dostępu do danych dla tabeli users.
 * Implementuje wzorzec Repository Pattern + Singleton.
 * 
 * Zgodność:
 * - BINGO D1: Zapytania SQL w warstwie Repository (nie w kontrolerach)
 * - RULES.md: UserRepository jako Singleton (wymaganie projektu)
 */
class UserRepository
{
    /**
     * PDO database connection
     */
    private PDO $db;
    
    /**
     * Singleton instance
     */
    private static ?UserRepository $instance = null;
    
    /**
     * Private constructor - zapobiega bezpośredniemu tworzeniu instancji
     */
    private function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Zapobiega klonowaniu Singletona
     */
    private function __clone() {}
    
    /**
     * Zapobiega unserialize Singletona
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * Zwraca jedyną instancję UserRepository (Singleton)
     * 
     * @return UserRepository
     */
    public static function getInstance(): UserRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Znajdź użytkownika po adresie email
     * 
     * Wykorzystywane przy logowaniu - sprawdzenie czy użytkownik istnieje
     * i pobranie jego hasła (hash) oraz roli.
     * 
     * @param string $email Adres email użytkownika
     * @return array|null Dane użytkownika lub null jeśli nie znaleziono
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "
                SELECT 
                    u.id,
                    u.email,
                    u.password,
                    u.role_id,
                    u.created_at,
                    r.name AS role_name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email
                LIMIT 1
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            // Zwraca null jeśli użytkownik nie został znaleziony
            return $user ?: null;
            
        } catch (PDOException $e) {
            error_log("UserRepository::findByEmail - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Znajdź użytkownika po ID
     * 
     * @param int $id Identyfikator użytkownika
     * @return array|null Dane użytkownika lub null jeśli nie znaleziono
     */
    public function findById(int $id): ?array
    {
        try {
            $sql = "
                SELECT 
                    u.id,
                    u.email,
                    u.role_id,
                    u.created_at,
                    r.name AS role_name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id
                LIMIT 1
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            return $user ?: null;
            
        } catch (PDOException $e) {
            error_log("UserRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Sprawdź czy email już istnieje w bazie
     * 
     * Wykorzystywane przy rejestracji - walidacja unikalności emaila.
     * 
     * @param string $email Adres email do sprawdzenia
     * @return bool True jeśli email istnieje, false w przeciwnym razie
     */
    public function emailExists(string $email): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            return ($result['count'] ?? 0) > 0;
            
        } catch (PDOException $e) {
            error_log("UserRepository::emailExists - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Utwórz nowego użytkownika
     * 
     * @param string $email Adres email
     * @param string $passwordHash Hash hasła (bcrypt)
     * @param int $roleId ID roli (1 = admin, 2 = user)
     * @return int|null ID utworzonego użytkownika lub null w przypadku błędu
     */
    public function create(string $email, string $passwordHash, int $roleId = 2): ?int
    {
        try {
            $sql = "
                INSERT INTO users (email, password, role_id)
                VALUES (:email, :password, :role_id)
                RETURNING id
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            return $result['id'] ?? null;
            
        } catch (PDOException $e) {
            error_log("UserRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Zaktualizuj hasło użytkownika
     * 
     * @param int $userId ID użytkownika
     * @param string $passwordHash Nowy hash hasła
     * @return bool True jeśli aktualizacja powiodła się
     */
    public function updatePassword(int $userId, string $passwordHash): bool
    {
        try {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("UserRepository::updatePassword - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Pobierz wszystkich użytkowników (dla panelu admina)
     * 
     * @return array Lista użytkowników
     */
    public function findAll(): array
    {
        try {
            $sql = "
                SELECT 
                    u.id,
                    u.email,
                    u.role_id,
                    u.created_at,
                    r.name AS role_name
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                ORDER BY u.created_at DESC
            ";
            
            $stmt = $this->db->query($sql);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("UserRepository::findAll - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Usuń użytkownika (soft delete możliwe w przyszłości)
     * 
     * @param int $userId ID użytkownika do usunięcia
     * @return bool True jeśli usunięcie powiodło się
     */
    public function delete(int $userId): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("UserRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Zaktualizuj rolę użytkownika
     * 
     * @param int $userId ID użytkownika
     * @param int $roleId Nowe ID roli (1 = admin, 2 = user)
     * @return bool True jeśli aktualizacja powiodła się
     */
    public function updateRole(int $userId, int $roleId): bool
    {
        try {
            $sql = "UPDATE users SET role_id = :role_id WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("UserRepository::updateRole - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Pobierz liczbę użytkowników według roli
     * 
     * @param int $roleId ID roli
     * @return int Liczba użytkowników
     */
    public function countByRole(int $roleId): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE role_id = :role_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            return (int)($result['count'] ?? 0);
            
        } catch (PDOException $e) {
            error_log("UserRepository::countByRole - Error: " . $e->getMessage());
            return 0;
        }
    }
}
