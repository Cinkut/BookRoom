<?php
/**
 * BookRoom - Front Controller
 * 
 * Główny punkt wejścia aplikacji
 * - Ładuje autoloader
 * - Startuje bezpieczną sesję (Security Bingo: HttpOnly, Secure, SameSite)
 * - Obsługuje routing (w przyszłości)
 */

// === 1. Ładowanie Autoloadera ===
require_once __DIR__ . '/../src/Autoload.php';

// === 2. Konfiguracja i start bezpiecznej sesji ===
// Security Bingo: Bezpieczne flagi sesji
ini_set('session.cookie_httponly', 1);  // HttpOnly - ochrona przed XSS
ini_set('session.cookie_secure', 1);    // Secure - tylko HTTPS (w produkcji)
ini_set('session.cookie_samesite', 'Strict'); // SameSite - ochrona przed CSRF
ini_set('session.use_strict_mode', 1);  // Strict mode - odrzucanie niezainicjowanych ID

// Start sesji
session_start();

// === 3. Test załadowania struktury MVC ===
echo '<h1>MVC Structure Loaded. Session Started.</h1>';
echo '<p>Session ID: ' . session_id() . '</p>';
echo '<p>Autoloader: ✓ Loaded</p>';
echo '<p>Security Flags: HttpOnly ✓ | Secure ✓ | SameSite=Strict ✓</p>';

// TODO: Tutaj później dodamy Router i obsługę kontrolerów
