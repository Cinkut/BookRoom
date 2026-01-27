<?php

namespace Controllers;

/**
 * ErrorController
 * 
 * Obsługuje strony błędów HTTP z odpowiednimi kodami statusu
 */
class ErrorController
{
    /**
     * 400 Bad Request
     */
    public static function badRequest(string $message = 'Nieprawidłowe żądanie'): void
    {
        http_response_code(400);
        self::renderError(400, 'Bad Request', $message);
    }
    
    /**
     * 401 Unauthorized
     */
    public static function unauthorized(string $message = 'Wymagane zalogowanie'): void
    {
        http_response_code(401);
        self::renderError(401, 'Unauthorized', $message);
    }
    
    /**
     * 403 Forbidden
     */
    public static function forbidden(string $message = 'Brak dostępu'): void
    {
        http_response_code(403);
        self::renderError(403, 'Forbidden', $message);
    }
    
    /**
     * 404 Not Found
     */
    public static function notFound(string $message = 'Nie znaleziono strony'): void
    {
        http_response_code(404);
        self::renderError(404, 'Not Found', $message);
    }
    
    /**
     * 500 Internal Server Error
     */
    public static function internalError(string $message = 'Błąd serwera'): void
    {
        http_response_code(500);
        self::renderError(500, 'Internal Server Error', $message);
    }
    
    /**
     * Renderuje stronę błędu
     */
    private static function renderError(int $code, string $title, string $message): void
    {
        // W produkcji nie pokazuj szczegółów błędów 500
        if ($code === 500 && !self::isDebugMode()) {
            $message = 'Wystąpił błąd serwera. Skontaktuj się z administratorem.';
        }
        
        ?>
        <!DOCTYPE html>
        <html lang="pl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Błąd <?= $code ?> - BookRoom</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                
                .error-container {
                    background: white;
                    border-radius: 20px;
                    padding: 60px 40px;
                    max-width: 500px;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                }
                
                .error-code {
                    font-size: 120px;
                    font-weight: 900;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    line-height: 1;
                    margin-bottom: 20px;
                }
                
                .error-title {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                
                .error-message {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }
                
                .error-button {
                    display: inline-block;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-decoration: none;
                    padding: 14px 30px;
                    border-radius: 10px;
                    font-weight: 600;
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                
                .error-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-code"><?= $code ?></div>
                <div class="error-title"><?= htmlspecialchars($title) ?></div>
                <div class="error-message"><?= htmlspecialchars($message) ?></div>
                <a href="/dashboard" class="error-button">Wróć do strony głównej</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Sprawdza czy aplikacja jest w trybie debug
     */
    private static function isDebugMode(): bool
    {
        return ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
    }
}
