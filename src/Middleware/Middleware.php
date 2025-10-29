<?php
namespace App\Middleware;

/**
 * Clase base para todos los middlewares
 */
abstract class Middleware {
    /**
     * Maneja la solicitud
     * 
     * @return bool True si la solicitud puede continuar, False para detenerla
     */
    abstract public function handle(): bool;
    
    /**
     * Redirige a una URL y termina la ejecución
     */
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
    
    /**
     * Responde con un error HTTP y termina la ejecución
     */
    protected function abort(int $code, string $message = ''): void {
        http_response_code($code);
        
        if ($code === 401) {
            echo json_encode(['error' => $message ?: 'No autenticado']);
        } elseif ($code === 403) {
            echo json_encode(['error' => $message ?: 'Acceso denegado']);
        } else {
            echo json_encode(['error' => $message ?: 'Error']);
        }
        
        exit;
    }
}
