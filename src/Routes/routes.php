<?php
/**
 * Archivo de definición de rutas de la aplicación
 */

use App\Routes\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SalesController;
use App\Controllers\HistorialController;
use App\Controllers\AgregarCajeroController;
use App\Controllers\ApiController;
use App\Controllers\Api\CostoController;
use App\Controllers\ProductController;
use App\Controllers\VariantController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

$router = new Router();

// ========================================
// Rutas de Autenticación (públicas)
// ========================================
$router->get('/', [AuthController::class, 'showLoginForm']);
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login'], [
    CsrfMiddleware::class
]);
$router->get('/logout', [AuthController::class, 'logout']);

// Página de acceso denegado
$router->get('/acceso-denegado', function() {
    require_once __DIR__ . '/../Views/errors/403.php';
});

// ========================================
// Rutas de Dashboard y Menú (requieren autenticación)
// ========================================
$router->get('/menu', [DashboardController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']) // Solo admin puede ver el dashboard completo
]);

$router->get('/dashboard', [DashboardController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']) // Solo admin puede ver el dashboard completo
]);

$router->get('/ventas', [SalesController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin', 'cajero']) // Admin y cajero pueden hacer ventas
]);

// Ruta legacy - redirige a /ventas
$router->get('/venta', [SalesController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin', 'cajero']) // Admin y cajero pueden hacer ventas
]);

$router->get('/historial', [HistorialController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']) // Solo admin puede ver historial
]);

$router->get('/agregarCajero', [AgregarCajeroController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']) // Solo admin puede ver historial
]);

$router->post('/agregarCajero', [AgregarCajeroController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']) // Solo admin puede ver historial
]);

// ========================================
// Rutas de API (JSON) - Requieren autenticación
// ========================================
$router->get('/api/dashboard', [ApiController::class, 'dashboardData'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);

$router->get('/api/productos', [ApiController::class, 'productos'], [
    AuthMiddleware::class
]);

// Costos endpoints
$router->get('/api/productos/{id}/costo', [CostoController::class, 'productoCosto'], [
    AuthMiddleware::class
]);

$router->get('/api/ingredientes/costos', [CostoController::class, 'ingredientesCostos'], [
    AuthMiddleware::class
]);

$router->get('/api/categorias', [ApiController::class, 'categorias'], [
    AuthMiddleware::class
]);

$router->get('/api/metodos-pago', [ApiController::class, 'metodosPago'], [
    AuthMiddleware::class
]);

// Imagen de producto (BLOB)
$router->get('/api/productos/{id}/imagen', [ApiController::class, 'productoImagen'], [
    AuthMiddleware::class
]);

$router->post('/api/productos/{id}/imagen', [ApiController::class, 'productoImagenUpload'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);

// ========================================
// Rutas de Productos
// ========================================
// Catálogo de Productos (CRUD - vista)
$router->get('/productos', [ProductController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->get('/productos/nuevo', [ProductController::class, 'create'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->post('/productos/guardar', [ProductController::class, 'store'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
$router->get('/productos/editar/{id}', [ProductController::class, 'edit'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->post('/productos/actualizar/{id}', [ProductController::class, 'update'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
$router->post('/productos/eliminar/{id}', [ProductController::class, 'delete'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);

// ========================================
// Rutas de Variantes por Producto
// ========================================
$router->get('/productos/{id}/variantes', [VariantController::class, 'index'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->get('/productos/{id}/variantes/nueva', [VariantController::class, 'create'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->post('/productos/{id}/variantes/guardar', [VariantController::class, 'store'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
$router->get('/productos/{id}/variantes/editar/{vid}', [VariantController::class, 'edit'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
$router->post('/productos/{id}/variantes/actualizar/{vid}', [VariantController::class, 'update'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
$router->post('/productos/{id}/variantes/estado/{vid}', [VariantController::class, 'changeState'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
$router->post('/productos/{id}/variantes/eliminar/{vid}', [VariantController::class, 'delete'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin']),
    CsrfMiddleware::class
]);
// $router->get('/productos/nuevo', [ProductController::class, 'create']);
// $router->post('/productos/guardar', [ProductController::class, 'store']);
// $router->get('/productos/editar/{id}', [ProductController::class, 'edit']);
// $router->post('/productos/actualizar/{id}', [ProductController::class, 'update']);
// $router->post('/productos/eliminar/{id}', [ProductController::class, 'delete']);

// ========================================
// Rutas de Ventas
// ========================================
$router->post('/api/ventas', [SalesController::class, 'procesarVenta'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin', 'cajero'])
]);

// ========================================
// Rutas de Historial
// ========================================
$router->get('/api/ventas/historial', [HistorialController::class, 'getVentas'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);

$router->get('/api/ventas/{id}', [HistorialController::class, 'getDetalle'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);

// ========================================
// Rutas de Historial
// ========================================
$router->get('/api/usuarios', [AgregarCajeroController::class, 'getUsuarios'], [
    AuthMiddleware::class,
    new RoleMiddleware(['admin'])
]);
// ========================================
// Rutas de Usuarios
// ========================================
// TODO: Implementar UserController
// $router->get('/usuarios', [UserController::class, 'index']);

return $router;
