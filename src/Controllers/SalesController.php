<?php
namespace App\Controllers;

use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\PaymentMethodService;

class SalesController {
    private $saleService;
    private $productService;
    private $categoryService;
    private $paymentService;
    
    public function __construct() {
        $this->saleService = new SaleService();
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
        $this->paymentService = new PaymentMethodService();
    }
    
    /**
     * Muestra la interfaz POS (Point of Sale)
     */
    public function index() {
        $user = [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario'],
            'rol' => $_SESSION['rol']
        ];
        
        require_once __DIR__ . '/../Views/sales/venta.php';
    }
    
    /**
     * API: Procesa una nueva venta
     */
    public function procesarVenta() {
        header('Content-Type: application/json');
        
        try {
            // Verificar autenticación
            if (!isset($_SESSION['usuario_id'])) {
                http_response_code(401);
                echo json_encode(['ok' => false, 'error' => 'No autenticado']);
                exit;
            }
            
            // Leer datos del request
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
                exit;
            }
            
            // Validar campos requeridos
            if (!isset($data['metodo_id']) || !isset($data['items'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'Faltan datos requeridos']);
                exit;
            }
            
            $userId = (int) $_SESSION['usuario_id'];
            $paymentMethodId = (int) $data['metodo_id'];
            $items = $data['items'];
            
            // Registrar venta usando el servicio
            $result = $this->saleService->registerSale($userId, $paymentMethodId, $items);
            
            http_response_code(201);
            echo json_encode([
                'ok' => true,
                'venta_id' => $result['id'],  // Cambiado de 'sale_id' a 'id'
                'codigo' => $result['code'],
                'total' => $result['total']
            ]);
            
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log("Error al procesar venta: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'Error al procesar la venta']);
        }
    }
}
