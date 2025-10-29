<?php
namespace App\Controllers;

class DashboardController {
    
    /**
     * Muestra el menú principal / dashboard
     */
    public function index() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /login");
            exit;
        }
        
        // Por ahora, mostrar una página simple
        require_once __DIR__ . '/../Views/dashboard/menu.php';
    }
    
    /**
     * Muestra la página de ventas
     */
    public function venta() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /login");
            exit;
        }
        
        // Por ahora, mostrar una página simple
        require_once __DIR__ . '/../Views/sales/venta.php';
    }
}
