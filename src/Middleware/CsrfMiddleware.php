<?php
namespace App\Middleware;

/**
 * Middleware para protección CSRF (Cross-Site Request Forgery)
 * Verifica que los formularios POST incluyan un token CSRF válido
 */
class CsrfMiddleware extends Middleware {
    
    /**
     * Verifica el token CSRF en peticiones POST
     * 
     * @return bool True si el token es válido o no es necesario, abort si es inválido
     */
    public function handle(): bool {
        // Solo verificar en peticiones POST, PUT, DELETE
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            return true; // No verificar en GET
        }
        
        // Generar token si no existe
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateToken();
        }
        
        // Obtener el token del request
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        // Verificar el token
        if (!$this->verifyToken($token)) {
            if ($this->isAjaxRequest()) {
                $this->abort(403, 'Token CSRF inválido o expirado');
            }
            
            // Redirigir con error
            $_SESSION['csrf_error'] = 'Token de seguridad inválido. Por favor, intenta nuevamente.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
        
        return true;
    }
    
    /**
     * Genera un nuevo token CSRF
     */
    private function generateToken(): string {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Verifica que el token sea válido
     */
    private function verifyToken(string $token): bool {
        if (empty($token)) {
            return false;
        }
        
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Verifica si la solicitud es AJAX o una petición de API
     */
    private function isAjaxRequest(): bool {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Función helper para obtener el token CSRF (uso en vistas)
     */
    public static function getToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Función helper para generar campo HTML con token CSRF
     */
    public static function field(): string {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
