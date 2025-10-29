<?php
namespace App\Middleware;

/**
 * Middleware para verificar permisos por rol
 * Verifica que el usuario tenga el rol necesario para acceder a una ruta
 */
class RoleMiddleware extends Middleware {
    
    private array $allowedRoles;
    
    /**
     * Constructor
     * 
     * @param string|array $allowedRoles Rol(es) permitido(s): 'admin', 'cajero', o ['admin', 'cajero']
     */
    public function __construct($allowedRoles) {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    }
    
    /**
     * Verifica si el usuario tiene el rol necesario
     * 
     * @return bool True si tiene permiso, abort/redirect si no
     */
    public function handle(): bool {
        // Primero verificar que esté autenticado
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
            if ($this->isAjaxRequest()) {
                $this->abort(401, 'No autenticado');
            }
            $this->redirect('/login');
        }
        
        $userRole = $_SESSION['rol'];
        
        // Verificar si el rol del usuario está en los roles permitidos
        if (!in_array($userRole, $this->allowedRoles, true)) {
            if ($this->isAjaxRequest()) {
                $this->abort(403, 'No tienes permisos para acceder a este recurso');
            }
            
            // Redirigir a página de acceso denegado
            $this->redirect('/acceso-denegado');
        }
        
        // Usuario tiene el rol necesario, continuar
        return true;
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
        
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        return false;
    }
}
