<?php
/**
 * BookRoom - Front Controller
 * 
 * Główny punkt wejścia aplikacji
 * - Ładuje autoloader
 * - Startuje bezpieczną sesję (Security Bingo: HttpOnly, Secure, SameSite)
 * - Obsługuje routing
 */

// === 1. Ładowanie Autoloadera ===
require_once __DIR__ . '/../src/Autoload.php';

// === 2. Konfiguracja i start bezpiecznej sesji ===
// Security Bingo: Bezpieczne flagi sesji
ini_set('session.cookie_httponly', 1);  // HttpOnly - ochrona przed XSS
ini_set('session.cookie_secure', 0);    // Secure - tylko HTTPS (w produkcji ustaw na 1)
ini_set('session.cookie_samesite', 'Strict'); // SameSite - ochrona przed CSRF
ini_set('session.use_strict_mode', 1);  // Strict mode - odrzucanie niezainicjowanych ID

// Start sesji
session_start();

// === 3. Router - prosty routing URL -> Controller ===

use Controllers\SecurityController;

// Pobranie URI i metody HTTP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Routing
switch ($uri) {
    // === Security Routes ===
    case '/':
    case '/login':
        $controller = new SecurityController();
        if ($method === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
    
    case '/logout':
        $controller = new SecurityController();
        $controller->logout();
        break;
    
    case '/register':
        $controller = new SecurityController();
        if ($method === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
    
    // === Dashboard (wymaga logowania) ===
    case '/dashboard':
        SecurityController::requireLogin();
        echo '<h1>Dashboard (User)</h1>';
        echo '<p>Witaj, ' . htmlspecialchars($_SESSION['user']['email']) . '!</p>';
        echo '<p>Rola: ' . htmlspecialchars($_SESSION['user']['role_name']) . '</p>';
        echo '<a href="/logout">Wyloguj</a>';
        break;
    
    case '/admin/dashboard':
        SecurityController::requireAdmin();
        echo '<h1>Dashboard (Admin)</h1>';
        echo '<p>Panel administracyjny</p>';
        echo '<a href="/logout">Wyloguj</a>';
        break;
    
    // === 404 Not Found ===
    default:
        http_response_code(404);
        echo '<h1>404 - Nie znaleziono strony</h1>';
        echo '<p>Strona <code>' . htmlspecialchars($uri) . '</code> nie istnieje.</p>';
        echo '<a href="/login">Powrót do logowania</a>';
        break;
}
