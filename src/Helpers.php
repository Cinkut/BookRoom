<?php
/**
 * Helper Functions
 * 
 * Globalne funkcje pomocnicze używane w aplikacji.
 */

/**
 * Escapuje dane wyjściowe przed wyświetleniem w HTML
 * Zapobiega atakom XSS
 * 
 * @param string $text Tekst do escapowania
 * @return string Bezpieczny tekst
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Zwraca wartość z tablicy lub wartość domyślną
 * 
 * @param array $array Tablica
 * @param string $key Klucz
 * @param mixed $default Wartość domyślna
 * @return mixed
 */
function arrayGet(array $array, string $key, $default = null)
{
    return $array[$key] ?? $default;
}
