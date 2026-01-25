<?php

/**
 * Database Class - Singleton Pattern
 * 
 * Zarządza połączeniem PDO z bazą danych PostgreSQL.
 * Wykorzystuje wzorzec Singleton aby zapewnić tylko jedno połączenie w całej aplikacji.
 * 
 * Konfiguracja z docker-compose.yml:
 * - Host: db (nazwa kontenera)
 * - User: dbuser
 * - Database: bookroom
 * - Password: dbpassword
 */
class Database
{
    /**
     * Singleton instance
     */
    private static ?Database $instance = null;
    
    /**
     * PDO connection object
     */
    private ?PDO $connection = null;
    
    /**
     * Konfiguracja bazy danych
     */
    private string $host = 'db';          // Nazwa kontenera Docker
    private string $database = 'bookroom';
    private string $username = 'dbuser';
    private string $password = 'dbpassword';
    private int $port = 5432;
    
    /**
     * Private constructor - Singleton pattern
     * Zapobiega tworzeniu instancji z zewnątrz klasy
     */
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Prevent cloning - Singleton pattern
     */
    private function __clone()
    {
        // Zapobieganie klonowaniu instancji
    }
    
    /**
     * Prevent unserialization - Singleton pattern
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Pobiera instancję klasy Database (Singleton)
     * 
     * @return Database Jednyna instancja klasy
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Nawiązuje połączenie z bazą danych PostgreSQL
     * 
     * @throws PDOException Gdy połączenie nie powiedzie się
     */
    private function connect(): void
    {
        try {
            // DSN (Data Source Name) dla PostgreSQL
            $dsn = sprintf(
                'pgsql:host=%s;port=%d;dbname=%s',
                $this->host,
                $this->port,
                $this->database
            );
            
            // Opcje PDO dla bezpieczeństwa i wygody
            $options = [
                // Tryb raportowania błędów - wyjątki
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                
                // Domyślny tryb pobierania - asocjacyjny
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Wyłączenie emulacji prepared statements (bezpieczeństwo)
                PDO::ATTR_EMULATE_PREPARES => false,
                
                // Ustawienie charset na UTF-8
                PDO::ATTR_PERSISTENT => false,
            ];
            
            // Utworzenie połączenia PDO
            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
            
        } catch (PDOException $e) {
            // Logowanie błędu (w produkcji nie wyświetlamy szczegółów)
            error_log("Database connection failed: " . $e->getMessage());
            
            // W środowisku deweloperskim pokazujemy szczegóły
            throw new PDOException(
                "Database connection failed. Please check your configuration."
            );
        }
    }
    
    /**
     * Pobiera obiekt połączenia PDO
     * 
     * @return PDO Obiekt połączenia z bazą danych
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Zamyka połączenie z bazą danych
     * (PDO zamyka się automatycznie, ale można wymusić ręcznie)
     */
    public function disconnect(): void
    {
        $this->connection = null;
    }
    
    /**
     * Testuje połączenie z bazą danych
     * 
     * @return bool True jeśli połączenie działa, false w przeciwnym razie
     */
    public function testConnection(): bool
    {
        try {
            $stmt = $this->getConnection()->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Connection test failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Pobiera informacje o wersji PostgreSQL
     * 
     * @return string|null Wersja PostgreSQL lub null jeśli błąd
     */
    public function getServerVersion(): ?string
    {
        try {
            $stmt = $this->getConnection()->query('SELECT version()');
            $result = $stmt->fetch();
            return $result['version'] ?? null;
        } catch (PDOException $e) {
            error_log("Failed to get server version: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Rozpoczyna transakcję
     * 
     * @return bool True jeśli transakcja rozpoczęta pomyślnie
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Zatwierdza transakcję
     * 
     * @return bool True jeśli transakcja zatwierdzona pomyślnie
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * Wycofuje transakcję
     * 
     * @return bool True jeśli transakcja wycofana pomyślnie
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }
}
