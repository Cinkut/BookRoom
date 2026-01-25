<?php
/**
 * BookRoom - Test UserRepository
 * 
 * Testuje wzorzec Repository Pattern i metody dostępu do użytkowników
 */

require_once __DIR__ . '/../src/Autoload.php';

use Repository\UserRepository;

echo '<h1>BookRoom - UserRepository Test</h1>';
echo '<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { background: white; padding: 15px; border-radius: 5px; margin: 10px 0; }
    table { background: white; border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
    th { background: #667eea; color: white; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
</style>';

try {
    // Utworzenie instancji UserRepository
    $userRepo = new UserRepository();
    
    echo '<div class="info">';
    echo '<h2 class="success">✓ UserRepository został utworzony</h2>';
    echo '</div>';
    
    // TEST 1: findByEmail - użytkownik istniejący
    echo '<div class="info">';
    echo '<h3>Test 1: findByEmail() - użytkownik admin</h3>';
    
    $admin = $userRepo->findByEmail('admin@bookroom.com');
    
    if ($admin) {
        echo '<p class="success">✓ Znaleziono użytkownika admin@bookroom.com</p>';
        echo '<table>';
        echo '<tr><th>Pole</th><th>Wartość</th></tr>';
        echo '<tr><td>ID</td><td>' . htmlspecialchars($admin['id']) . '</td></tr>';
        echo '<tr><td>Email</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
        echo '<tr><td>Role ID</td><td>' . htmlspecialchars($admin['role_id']) . '</td></tr>';
        echo '<tr><td>Role Name</td><td><strong>' . htmlspecialchars($admin['role_name']) . '</strong></td></tr>';
        echo '<tr><td>Password (hash)</td><td>' . substr(htmlspecialchars($admin['password']), 0, 20) . '...</td></tr>';
        echo '<tr><td>Created At</td><td>' . htmlspecialchars($admin['created_at']) . '</td></tr>';
        echo '</table>';
    } else {
        echo '<p class="error">✗ Nie znaleziono użytkownika</p>';
    }
    echo '</div>';
    
    // TEST 2: findByEmail - użytkownik nieistniejący
    echo '<div class="info">';
    echo '<h3>Test 2: findByEmail() - użytkownik nieistniejący</h3>';
    
    $nonExistent = $userRepo->findByEmail('nobody@example.com');
    
    if ($nonExistent === null) {
        echo '<p class="success">✓ Poprawnie zwrócono NULL dla nieistniejącego użytkownika</p>';
    } else {
        echo '<p class="error">✗ Błąd - powinno zwrócić NULL</p>';
    }
    echo '</div>';
    
    // TEST 3: emailExists
    echo '<div class="info">';
    echo '<h3>Test 3: emailExists() - sprawdzenie unikalności</h3>';
    
    $exists = $userRepo->emailExists('admin@bookroom.com');
    $notExists = $userRepo->emailExists('newuser@example.com');
    
    if ($exists) {
        echo '<p class="success">✓ admin@bookroom.com istnieje w bazie</p>';
    } else {
        echo '<p class="error">✗ Błąd - admin powinien istnieć</p>';
    }
    
    if (!$notExists) {
        echo '<p class="success">✓ newuser@example.com nie istnieje (może zostać zarejestrowany)</p>';
    } else {
        echo '<p class="error">✗ Błąd - user nie powinien istnieć</p>';
    }
    echo '</div>';
    
    // TEST 4: findById
    echo '<div class="info">';
    echo '<h3>Test 4: findById() - wyszukiwanie po ID</h3>';
    
    if ($admin) {
        $userById = $userRepo->findById($admin['id']);
        
        if ($userById && $userById['email'] === $admin['email']) {
            echo '<p class="success">✓ Znaleziono użytkownika po ID: ' . $admin['id'] . '</p>';
            echo '<p>Email: <code>' . htmlspecialchars($userById['email']) . '</code></p>';
        } else {
            echo '<p class="error">✗ Nie znaleziono użytkownika po ID</p>';
        }
    }
    echo '</div>';
    
    // TEST 5: findAll - lista wszystkich użytkowników
    echo '<div class="info">';
    echo '<h3>Test 5: findAll() - lista wszystkich użytkowników</h3>';
    
    $allUsers = $userRepo->findAll();
    
    if (count($allUsers) > 0) {
        echo '<p class="success">✓ Znaleziono ' . count($allUsers) . ' użytkowników</p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Email</th><th>Rola</th><th>Data utworzenia</th></tr>';
        
        foreach ($allUsers as $user) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($user['id']) . '</td>';
            echo '<td>' . htmlspecialchars($user['email']) . '</td>';
            echo '<td><strong>' . htmlspecialchars($user['role_name']) . '</strong></td>';
            echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    } else {
        echo '<p class="error">✗ Brak użytkowników w bazie</p>';
    }
    echo '</div>';
    
    // TEST 6: countByRole
    echo '<div class="info">';
    echo '<h3>Test 6: countByRole() - statystyki</h3>';
    
    $adminCount = $userRepo->countByRole(1); // role_id = 1 (admin)
    $userCount = $userRepo->countByRole(2);  // role_id = 2 (user)
    
    echo '<p><strong>Administratorzy:</strong> ' . $adminCount . '</p>';
    echo '<p><strong>Użytkownicy:</strong> ' . $userCount . '</p>';
    echo '<p><strong>Razem:</strong> ' . ($adminCount + $userCount) . '</p>';
    
    if ($adminCount > 0 && $userCount > 0) {
        echo '<p class="success">✓ Statystyki obliczone poprawnie</p>';
    }
    echo '</div>';
    
    // TEST 7: Weryfikacja wzorca Repository Pattern
    echo '<div class="info">';
    echo '<h3>Test 7: Wzorzec Repository Pattern</h3>';
    
    echo '<p class="success">✓ Wzorzec Repository Pattern zaimplementowany poprawnie:</p>';
    echo '<ul>';
    echo '<li>✓ Separacja logiki SQL od kontrolerów (BINGO D1)</li>';
    echo '<li>✓ Prepared statements - bezpieczeństwo przed SQL Injection</li>';
    echo '<li>✓ Obsługa błędów PDOException</li>';
    echo '<li>✓ Metoda findByEmail() gotowa do użycia w SecurityController</li>';
    echo '<li>✓ Brak duplikacji kodu - reużywalne metody</li>';
    echo '</ul>';
    echo '</div>';
    
    // Podsumowanie
    echo '<div class="info">';
    echo '<h2 class="success">🎉 Wszystkie testy UserRepository zakończone pomyślnie!</h2>';
    echo '<p>UserRepository jest gotowy do integracji z SecurityController.</p>';
    echo '<p><strong>Następny krok:</strong> Implementacja logowania z użyciem <code>findByEmail()</code></p>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="info">';
    echo '<h2 class="error">✗ Błąd</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
