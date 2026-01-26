<?php
/**
 * BookRoom - Front Controller
 * 
 * Główny punkt wejścia aplikacji
 * - Ładuje autoloader
 * - Startuje bezpieczną sesję (Security Bingo: HttpOnly, Secure, SameSite)
 * - Używa Router do obsługi ścieżek
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

// === 3. Router - rejestracja routes i dispatch ===

$router = new Router();

// === Security Routes ===
$router->get('/', 'SecurityController@showLogin');
$router->get('/login', 'SecurityController@showLogin');
$router->post('/login', 'SecurityController@login');
$router->get('/logout', 'SecurityController@logout');


// === Dashboard Routes (wymaga logowania) ===
$router->get('/dashboard', 'DashboardController@index', ['auth']);
$router->get('/admin/dashboard', 'DashboardController@admin', ['auth', 'admin']);
$router->post('/admin/users/create', 'DashboardController@createUser', ['auth', 'admin']);

// === Room Routes ===
$router->get('/rooms', 'RoomController@index', ['auth']);
$router->get('/rooms/{id}', 'RoomController@show', ['auth']);
$router->get('/rooms/{id}/book', 'RoomController@book', ['auth']);
$router->post('/rooms/{id}/book', 'RoomController@processBook', ['auth']);

// === User Profile Routes ===
$router->get('/profile', 'ProfileController@index', ['auth']);

// === Dispatch - uruchom odpowiedni kontroler ===
$router->dispatch();
