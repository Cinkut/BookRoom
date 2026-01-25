<?php
/**
 * BookRoom Autoloader
 * 
 * Automatyczne ładowanie klas z katalogu /src
 * Zgodne z zasadą: nazwy klas = nazwy plików (PSR-4 style)
 */

spl_autoload_register(function ($className) {
    // Podstawowa ścieżka do katalog /src
    $baseDir = __DIR__ . '/';
    
    // Zamiana namespace separatorów na separatory katalogów
    // Np. Controllers\SecurityController -> Controllers/SecurityController.php
    $file = $baseDir . str_replace('\\', '/', $className) . '.php';
    
    // Sprawdzenie czy plik istnieje i wczytanie go
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});
