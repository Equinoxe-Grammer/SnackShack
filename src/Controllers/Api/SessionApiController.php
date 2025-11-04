<?php
namespace App\Controllers\Api;

use App\Services\SessionService;

/**
 * Controlador API para gestión de sesiones
 */
class SessionApiController
{
    /**
     * Obtiene información de la sesión actual
     * GET /api/session/info
     */
    public function info(): void
    {
        header('Content-Type: application/json');
        
        if (!SessionService::isActive()) {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'error' => 'No hay sesión activa'
            ]);
            return;
        }
        
        if (!SessionService::validate()) {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'error' => 'Sesión expirada o inválida'
            ]);
            return;
        }
        
        $info = SessionService::getInfo();
        
        echo json_encode([
            'ok' => true,
            'session' => $info
        ]);
    }
    
    /**
     * Extiende la sesión actual (reinicia el contador de 24 horas)
     * POST /api/session/extend
     */
    public function extend(): void
    {
        header('Content-Type: application/json');
        
        if (!SessionService::isActive() || !SessionService::validate()) {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'error' => 'Sesión inválida'
            ]);
            return;
        }
        
        SessionService::extend();
        
        $info = SessionService::getInfo();
        
        echo json_encode([
            'ok' => true,
            'message' => 'Sesión extendida por 24 horas más',
            'session' => $info
        ]);
    }
    
    /**
     * Verifica si la sesión sigue activa (para polling)
     * GET /api/session/check
     */
    public function check(): void
    {
        header('Content-Type: application/json');
        
        $isValid = SessionService::isActive() && SessionService::validate();
        
        echo json_encode([
            'ok' => true,
            'active' => $isValid,
            'remaining_seconds' => $isValid ? SessionService::getRemainingTime() : 0
        ]);
    }
}
