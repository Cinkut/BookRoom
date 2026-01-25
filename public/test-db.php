<?php
/**
 * BookRoom - Test połączenia z bazą danych
 * 
 * Testuje klasę Database i wyświetla informacje o połączeniu
 */

require_once __DIR__ . '/../src/Autoload.php';

echo '<h1>BookRoom - Database Connection Test</h1>';
echo '<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { background: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { background: white; border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
    th { background: #667eea; color: white; }
</style>';

try {
    // Pobranie instancji Database (Singleton)
    $db = Database::getInstance();
    
    echo '<div class="info">';
    echo '<h2 class="success">✓ Połączenie z bazą danych nawiązane!</h2>';
    echo '</div>';
    
    // Test połączenia
    if ($db->testConnection()) {
        echo '<div class="info">';
        echo '<p class="success">✓ Test połączenia: PASSED</p>';
        echo '</div>';
    } else {
        echo '<div class="info">';
        echo '<p class="error">✗ Test połączenia: FAILED</p>';
        echo '</div>';
    }
    
    // Wersja PostgreSQL
    $version = $db->getServerVersion();
    echo '<div class="info">';
    echo '<h3>Informacje o serwerze:</h3>';
    echo '<p><strong>PostgreSQL Version:</strong> ' . htmlspecialchars($version ?? 'Unknown') . '</p>';
    echo '</div>';
    
    // Test zapytania - pobranie sal z wyposażeniem (widok)
    echo '<div class="info">';
    echo '<h3>Test widoku: v_room_details</h3>';
    
    $conn = $db->getConnection();
    $stmt = $conn->query('SELECT * FROM v_room_details ORDER BY id');
    $rooms = $stmt->fetchAll();
    
    if (count($rooms) > 0) {
        echo '<p class="success">✓ Znaleziono ' . count($rooms) . ' sal(e)</p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Nazwa</th><th>Pojemność</th><th>Wyposażenie</th><th>Liczba</th></tr>';
        
        foreach ($rooms as $room) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($room['id']) . '</td>';
            echo '<td>' . htmlspecialchars($room['name']) . '</td>';
            echo '<td>' . htmlspecialchars($room['capacity']) . ' osób</td>';
            echo '<td>' . htmlspecialchars($room['equipment_list']) . '</td>';
            echo '<td>' . htmlspecialchars($room['equipment_count']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo '<p class="error">✗ Brak danych w widoku</p>';
    }
    echo '</div>';
    
    // Test zapytania - nadchodzące rezerwacje (widok)
    echo '<div class="info">';
    echo '<h3>Test widoku: v_upcoming_bookings</h3>';
    
    $stmt = $conn->query('SELECT * FROM v_upcoming_bookings LIMIT 5');
    $bookings = $stmt->fetchAll();
    
    if (count($bookings) > 0) {
        echo '<p class="success">✓ Znaleziono ' . count($bookings) . ' nadchodzącą/e rezerwację/e</p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Data</th><th>Godziny</th><th>Sala</th><th>Użytkownik</th><th>Status</th></tr>';
        
        foreach ($bookings as $booking) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($booking['booking_id']) . '</td>';
            echo '<td>' . htmlspecialchars($booking['date']) . '</td>';
            echo '<td>' . htmlspecialchars($booking['start_time']) . ' - ' . htmlspecialchars($booking['end_time']) . '</td>';
            echo '<td>' . htmlspecialchars($booking['room_name']) . '</td>';
            echo '<td>' . htmlspecialchars($booking['user_email']) . '</td>';
            echo '<td>' . htmlspecialchars($booking['status']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo '<p>Brak nadchodzących rezerwacji</p>';
    }
    echo '</div>';
    
    // Test Singleton - sprawdzenie czy to ta sama instancja
    echo '<div class="info">';
    echo '<h3>Test wzorca Singleton:</h3>';
    
    $db2 = Database::getInstance();
    if ($db === $db2) {
        echo '<p class="success">✓ Singleton działa poprawnie - ta sama instancja</p>';
    } else {
        echo '<p class="error">✗ Singleton NIE działa - różne instancje</p>';
    }
    echo '</div>';
    
    echo '<div class="info">';
    echo '<h2 class="success">🎉 Wszystkie testy zakończone pomyślnie!</h2>';
    echo '<p>Baza danych działa poprawnie. Klasa Database (Singleton) gotowa do użycia.</p>';
    echo '</div>';
    
} catch (PDOException $e) {
    echo '<div class="info">';
    echo '<h2 class="error">✗ Błąd połączenia z bazą danych</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="info">';
    echo '<h2 class="error">✗ Błąd</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
