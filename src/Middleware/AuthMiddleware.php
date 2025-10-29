<?php
namespace App\Middleware;

/**
 * Middleware para verificar autenticación
 * Verifica que el usuario tenga una sesión activa
 */
class AuthMiddleware extends Middleware {
    
    /**
     * Verifica si el usuario está autenticado
     * 
     * @return bool True si está autenticado, redirige a login si no
     */
    public function handle(): bool {
        // Verificar si existe una sesión activa
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario'])) {
            // Verificar si es una petición AJAX/API
            if ($this->isAjaxRequest()) {
                $this->abort(401, 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }
            
            // Guardar la URL a la que intentaba acceder para redirigir después del login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            // Redirigir al login
            $this->redirect('/login');
        }
        
        // Usuario autenticado, continuar
        return true;
    }
    
    /**
     * Verifica si la solicitud es AJAX o una petición de API
     */
    private function isAjaxRequest(): bool {
        // Verificar header X-Requested-With
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        // Verificar si es una ruta de API
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') !== false) {
            return true;
        }
        
        // Verificar header Accept para JSON
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        return false;
    }
}
