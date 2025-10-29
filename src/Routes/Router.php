<?php
namespace App\Routes;

class Router {
    private $routes = [];
    private $middleware = [];
    
    /**
     * Registra una ruta GET
     */
    public function get($path, $callback, $middleware = []) {
        $this->routes['GET'][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Registra una ruta POST
     */
    public function post($path, $callback, $middleware = []) {
        $this->routes['POST'][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Registra middleware global para todas las rutas
     */
    public function addGlobalMiddleware($middleware) {
        $this->middleware[] = $middleware;
    }
    
    /**
     * Despacha la solicitud al controlador correspondiente
     */
    public function dispatch() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Debug temporalmente habilitado
        error_log("Router Debug - Method: $method, URI: $uri");
        
        // Buscar coincidencia exacta
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            
            // Ejecutar middlewares globales
            $this->runMiddleware($this->middleware);
            
            // Ejecutar middlewares de la ruta
            if (isset($route['middleware'])) {
                $this->runMiddleware($route['middleware']);
            }
            
            // Ejecutar callback
            return $this->executeCallback($route['callback'] ?? $route);
        }
        
        // Buscar coincidencia con parámetros dinámicos
        foreach ($this->routes[$method] ?? [] as $path => $route) {
            $pattern = $this->convertToRegex($path);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remover la coincidencia completa
                
                // Ejecutar middlewares globales
                $this->runMiddleware($this->middleware);
                
                // Ejecutar middlewares de la ruta
                if (isset($route['middleware'])) {
                    $this->runMiddleware($route['middleware']);
                }
                
                // Ejecutar callback
                return $this->executeCallback($route['callback'] ?? $route, $matches);
            }
        }
        
        // Ruta no encontrada
        $this->notFound();
    }
    
    /**
     * Ejecuta una lista de middlewares
     */
    private function runMiddleware(array $middlewares) {
        foreach ($middlewares as $middleware) {
            // Si es una instancia de clase, ejecutar directamente
            if (is_object($middleware)) {
                if (!$middleware->handle()) {
                    exit; // Middleware detuvo la ejecución
                }
            }
            // Si es un string, instanciar la clase
            elseif (is_string($middleware) && class_exists($middleware)) {
                $instance = new $middleware();
                if (!$instance->handle()) {
                    exit; // Middleware detuvo la ejecución
                }
            }
        }
    }
    
    /**
     * Convierte una ruta con parámetros a expresión regular
     */
    private function convertToRegex($route) {
        $route = preg_replace('/\{(\w+)\}/', '([^/]+)', $route);
        return '#^' . $route . '$#';
    }
    
    /**
     * Ejecuta el callback de la ruta
     */
    private function executeCallback($callback, $params = []) {
        try {
            // Si es un array con estructura de ruta, extraer el callback
            if (is_array($callback) && isset($callback['callback'])) {
                $callback = $callback['callback'];
            }
            
            if (is_callable($callback)) {
                return call_user_func_array($callback, $params);
            }
            
            if (is_array($callback)) {
                list($controller, $method) = $callback;
                
                if (!class_exists($controller)) {
                    throw new \Exception("Controlador no encontrado: $controller");
                }
                
                $controllerInstance = new $controller();
                
                if (!method_exists($controllerInstance, $method)) {
                    throw new \Exception("Método $method no encontrado en $controller");
                }
                
                return call_user_func_array([$controllerInstance, $method], $params);
            }
            
            throw new \Exception("Callback no válido para la ruta");
        } catch (\Exception $e) {
            error_log("Error ejecutando callback: " . $e->getMessage());
            http_response_code(500);
            echo "<h1>Error 500</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']) {
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            }
            exit;
        }
    }
    
    /**
     * Maneja rutas no encontradas (404)
     */
    private function notFound() {
        http_response_code(404);
        
        if (file_exists(__DIR__ . '/../Views/errors/404.php')) {
            require_once __DIR__ . '/../Views/errors/404.php';
        } else {
            echo '<h1>404 - Página no encontrada</h1>';
        }
        exit;
    }
}
