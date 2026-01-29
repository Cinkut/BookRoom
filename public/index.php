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

// === 1.1 Ładowanie funkcji pomocniczych ===
require_once __DIR__ . '/../src/Helpers.php';

// === 2. Inicjalizacja bezpieczeństwa ===
use Security\SecurityConfig;

// Wymuszenie HTTPS (poza środowiskiem lokalnym)
SecurityConfig::enforceHttps();

// Bezpieczna konfiguracja sesji (HttpOnly, Secure, SameSite)
SecurityConfig::initSecureSession();

// Nagłówki bezpieczeństwa HTTP
SecurityConfig::setSecurityHeaders();

// === 3. Router - rejestracja routes i dispatch ===

$router = new Router();

// === Security Routes ===
$router->get('/', 'SecurityController@showLogin');
$router->get('/login', 'SecurityController@showLogin');
$router->post('/login', 'SecurityController@login');
$router->get('/logout', 'SecurityController@logout');

// === Password Change Routes ===
$router->get('/change-password', 'ChangePasswordController@showForm', ['auth']);
$router->post('/change-password', 'ChangePasswordController@changePassword', ['auth']);


// === Dashboard Routes (wymaga logowania) ===
$router->get('/dashboard', 'DashboardController@index', ['auth']);
$router->get('/admin/dashboard', 'DashboardController@admin', ['auth', 'admin']);
$router->post('/admin/users/create', 'DashboardController@createUser', ['auth', 'admin']);
$router->post('/admin/users/delete', 'DashboardController@deleteUser', ['auth', 'admin']);
$router->post('/admin/users/update-role', 'DashboardController@updateUserRole', ['auth', 'admin']);

// === Room Routes ===
$router->get('/rooms', 'RoomController@index', ['auth']);
$router->get('/rooms/{id}', 'RoomController@show', ['auth']);
$router->get('/rooms/{id}/book', 'RoomController@book', ['auth']);
$router->post('/rooms/{id}/book', 'RoomController@processBook', ['auth']);

// === API Routes ===
$router->get('/api/rooms/{id}/bookings', 'ApiController@getRoomBookings', ['auth']);

// === User Profile Routes ===
$router->get('/profile', 'ProfileController@index', ['auth']);
$router->post('/profile/update', 'ProfileController@updateProfile', ['auth']);
$router->post('/bookings/cancel', 'ProfileController@cancelBooking', ['auth']);

// === Dispatch - uruchom odpowiedni kontroler ===
$router->dispatch();
