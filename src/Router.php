<?php

/**
 * Router Class
 * 
 * Prosty router do obsługi URL -> Controller@method
 * 
 * Przykład użycia:
 * $router = new Router();
 * $router->get('/rooms', 'RoomController@index');
 * $router->post('/rooms/book', 'RoomController@book');
 * $router->dispatch();
 */
class Router
{
    /**
     * Zarejestrowane routes (GET)
     */
    private array $routes = [];
    
    /**
     * Zarejestrowane routes (POST)
     */
    private array $postRoutes = [];
    
    /**
     * Aktualna ścieżka URL
     */
    private string $currentPath;
    
    /**
     * Aktualna metoda HTTP
     */
    private string $currentMethod;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->currentMethod = $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Rejestruje route dla GET
     * 
     * @param string $path Ścieżka URL (np. '/rooms', '/rooms/{id}')
     * @param string $handler Handler w formacie 'Controller@method'
     * @param array $middleware Opcjonalne middleware (np. ['auth', 'admin'])
     */
    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->routes[$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Rejestruje route dla POST
     * 
     * @param string $path Ścieżka URL
     * @param string $handler Handler w formacie 'Controller@method'
     * @param array $middleware Opcjonalne middleware
     */
    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->postRoutes[$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Dispatch - uruchom odpowiedni handler dla obecnego URL
     * 
     * @return mixed
     */
    public function dispatch()
    {
        // Wybierz właściwą tablicę routes na podstawie metody HTTP
        $routes = $this->currentMethod === 'POST' ? $this->postRoutes : $this->routes;
        
        // Szukaj dokładnego dopasowania
        if (isset($routes[$this->currentPath])) {
            return $this->executeRoute($routes[$this->currentPath]);
        }
        
        // Szukaj dopasowania z parametrami (np. /rooms/{id})
        foreach ($routes as $path => $route) {
            $params = $this->matchRoute($path, $this->currentPath);
            if ($params !== false) {
                return $this->executeRoute($route, $params);
            }
        }
        
        // 404 - nie znaleziono route
        $this->notFound();
    }
    
    /**
     * Dopasuj route z parametrami
     * 
     * Przykład: /rooms/{id} dopasuje /rooms/5 i zwróci ['id' => '5']
     * 
     * @param string $pattern Wzorzec route (np. '/rooms/{id}')
     * @param string $path Rzeczywista ścieżka (np. '/rooms/5')
     * @return array|false Parametry lub false jeśli nie pasuje
     */
    private function matchRoute(string $pattern, string $path)
    {
        // Zamień {param} na regex
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $path, $matches)) {
            // Wyciągnij nazwy parametrów
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $pattern, $paramNames);
            
            $params = [];
            for ($i = 0; $i < count($paramNames[1]); $i++) {
                $params[$paramNames[1][$i]] = $matches[$i + 1];
            }
            
            return $params;
        }
        
        return false;
    }
    
    /**
     * Wykonaj route - uruchom kontroler i metodę
     * 
     * @param array $route Informacje o route (handler, middleware)
     * @param array $params Parametry z URL
     */
    private function executeRoute(array $route, array $params = []): void
    {
        // Uruchom middleware
        $this->runMiddleware($route['middleware']);
        
        // Parsuj handler (Controller@method)
        [$controllerName, $method] = explode('@', $route['handler']);
        
        // Dodaj namespace do kontrolera
        $controllerClass = 'Controllers\\' . $controllerName;
        
        // Sprawdź czy klasa istnieje
        if (!class_exists($controllerClass)) {
            $this->error("Controller {$controllerClass} not found");
            return;
        }
        
        // Utwórz instancję kontrolera
        $controller = new $controllerClass();
        
        // Sprawdź czy metoda istnieje
        if (!method_exists($controller, $method)) {
            $this->error("Method {$method} not found in {$controllerClass}");
            return;
        }
        
        // Wywołaj metodę z parametrami
        if (empty($params)) {
            $controller->$method();
        } else {
            $controller->$method($params);
        }
    }
    
    /**
     * Uruchom middleware
     * 
     * @param array $middleware Lista middleware do uruchomienia
     */
    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            switch ($mw) {
                case 'auth':
                    \Controllers\SecurityController::requireLogin();
                    break;
                    
                case 'admin':
                    \Controllers\SecurityController::requireAdmin();
                    break;
                    
                // Tutaj można dodać więcej middleware
            }
        }
    }
    
    /**
     * 404 Not Found
     */
    private function notFound(): void
    {
        \Controllers\ErrorController::notFound('Strona nie została znaleziona.');
    }
    
    /**
     * Wyświetl błąd
     * 
     * @param string $message Wiadomość błędu
     */
    private function error(string $message): void
    {
        error_log("Router Error: " . $message);
        \Controllers\ErrorController::internalError($message);
    }
    
    /**
     * Przekierowanie do innego URL
     * 
     * @param string $path Ścieżka do przekierowania
     */
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
    
    /**
     * Pobierz parametr z URL
     * 
     * @param string $key Klucz parametru
     * @param mixed $default Wartość domyślna
     * @return mixed
     */
    public static function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
