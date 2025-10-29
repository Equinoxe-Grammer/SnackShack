<?php
namespace App\Controllers;

use App\Services\SaleService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\PaymentMethodService;

class AgregarCajeroController {
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

    public function index() {
        $user = [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario'],
            'rol' => $_SESSION['rol']
        ];
        
        require_once __DIR__ . '/../Views/sales/agregarCajero.php';
    }
}
