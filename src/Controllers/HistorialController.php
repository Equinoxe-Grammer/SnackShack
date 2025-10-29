<?php
namespace App\Controllers;

use App\Services\SaleService;
use App\Services\CategoryService;

class HistorialController {
    private $saleService;
    private $categoryService;
    
    public function __construct() {
        $this->saleService = new SaleService();
        $this->categoryService = new CategoryService();
    }
    
    /**
     * Muestra la vista del historial de ventas
     */
    public function index() {
        $user = [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario'],
            'rol' => $_SESSION['rol']
        ];
        
        require_once __DIR__ . '/../Views/sales/historial.php';
    }
    
    /**
     * API: Retorna ventas con filtros aplicados
     */
    public function getVentas() {
        header('Content-Type: application/json');
        
        try {
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'No autenticado']);
                exit;
            }
            
            // Obtener parámetros de filtros
            $filters = [
                'search' => $_GET['q'] ?? '',
                'date' => $_GET['fecha'] ?? '',
                'date_from' => $_GET['fecha_desde'] ?? '',
                'date_to' => $_GET['fecha_hasta'] ?? '',
                'category' => $_GET['categoria_id'] ?? '',
                'payment_method' => $_GET['metodo_id'] ?? ''
            ];
            
            // Obtener datos del servicio
            $result = $this->saleService->getHistory($filters);
            
            // Transformar ventas al formato esperado
            $ventas = array_map(function($sale) {
                return [
                    'venta_id' => $sale->id,
                    'codigo' => $sale->code,
                    'fecha' => $sale->date,
                    'hora' => $sale->time,
                    'usuario' => $sale->user,
                    'metodo_pago' => $sale->paymentMethod,
                    'total' => (float) $sale->total,
                    'items' => array_map(function($item) {
                        return [
                            'producto' => $item->productName ?? 'Producto',
                            'variante' => $item->variantName ?? '',
                            'cantidad' => $item->quantity,
                            'precio_unitario' => (float) $item->unitPrice,
                            'subtotal' => (float) $item->subtotal
                        ];
                    }, $sale->items)
                ];
            }, $result['sales']);
            
            // Transformar resumen
            $summary = $result['summary'];
            $resumen = [
                'total_ventas' => isset($summary['total']) ? (float) $summary['total'] : 0.0,
                'num_ventas' => isset($summary['sales']) ? (int) $summary['sales'] : 0,
                'promedio' => isset($summary['average']) ? (float) $summary['average'] : 0.0
            ];
            
            echo json_encode([
                'ventas' => $ventas,
                'resumen' => $resumen
            ]);
            
        } catch (\Exception $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar el historial']);
        }
    }
    
    /**
     * API: Retorna detalle de una venta específica
     */
    public function getDetalle($id) {
        header('Content-Type: application/json');
        
        try {
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'No autenticado']);
                exit;
            }
            
            $ventaId = (int) $id;
            
            if ($ventaId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de venta inválido']);
                exit;
            }
            
            // Obtener detalle de la venta
            $sale = $this->saleService->getSaleDetails($ventaId);
            
            if (!$sale) {
                http_response_code(404);
                echo json_encode(['error' => 'Venta no encontrada']);
                exit;
            }
            
            // Transformar al formato esperado
            $detalle = [
                'venta_id' => $sale->id,
                'codigo' => $sale->code,
                'fecha' => $sale->date,
                'hora' => $sale->time,
                'usuario' => $sale->user,
                'metodo_pago' => $sale->paymentMethod,
                'total' => (float) $sale->total,
                'items' => array_map(function($item) {
                    return [
                        'producto' => $item->productName ?? 'Producto',
                        'variante' => $item->variantName ?? '',
                        'cantidad' => $item->quantity,
                        'precio_unitario' => (float) $item->unitPrice,
                        'subtotal' => (float) $item->subtotal
                    ];
                }, $sale->items)
            ];
            
            echo json_encode($detalle);
            
        } catch (\Exception $e) {
            error_log("Error al obtener detalle de venta: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Error al cargar el detalle de la venta']);
        }
    }
}
