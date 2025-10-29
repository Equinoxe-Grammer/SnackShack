<?php
namespace App\Controllers;

use App\Middleware\CsrfMiddleware;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use PDOException;

class ProductController
{
    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $user = [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'cajero',
        ];

        $service = new ProductService();
        $products = $service->getAllWithVariants();

        // Calcular costo de producción, precio final, margen y desglose IVA para cada producto
        try {
            $costoService = new \App\Services\CostoService(\App\Database\Connection::get());
            $impuestosService = new \App\Services\ImpuestosService();
            foreach ($products as $prod) {
                // precio_final por defecto: mínimo entre variantes si existen
                $precioFinal = null;
                if (!empty($prod->variants)) {
                    $prices = array_map(fn($v) => $v->price, $prod->variants);
                    $precioFinal = count($prices) ? min($prices) : null;
                }

                // costo de producción
                try {
                    $costo = $costoService->costoProductoNeto($prod->id);
                    $prod->costo_produccion = is_numeric($costo) ? round((float)$costo, 2) : null;
                } catch (\Throwable $e) {
                    $prod->costo_produccion = null;
                }

                // Asignar precio_final si lo tenemos
                $prod->precio_final = is_numeric($precioFinal) ? round((float)$precioFinal, 2) : null;

                // Desglose IVA y margen
                if ($prod->precio_final !== null) {
                    $desglose = $impuestosService->desgloseIVA15((float)$prod->precio_final);
                    $prod->neto = $desglose['neto'];
                    $prod->iva = $desglose['iva'];
                } else {
                    $prod->neto = null;
                    $prod->iva = null;
                }

                if ($prod->precio_final !== null && $prod->costo_produccion !== null) {
                    $prod->margen = round($prod->precio_final - $prod->costo_produccion, 2);
                    $prod->margenPct = $prod->precio_final != 0 ? round(($prod->margen / $prod->precio_final) * 100, 2) : null;
                } else {
                    $prod->margen = null;
                    $prod->margenPct = null;
                }
            }
        } catch (\Throwable $e) {
            // Si no es posible calcular costos por cualquier motivo, no detener la página
        }

        $categoryRepo = new CategoryRepository();
        $categories = $categoryRepo->findAll();

        $csrf = CsrfMiddleware::getToken();
        require __DIR__ . '/../Views/products/index.php';
    }

    public function create()
    {
        if (!isset($_SESSION['usuario_id'])) { header('Location: /login'); exit; }
        $user = [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'cajero',
        ];
        $categoryRepo = new CategoryRepository();
        $categories = $categoryRepo->findAll();
        $product = null;
        $csrf = CsrfMiddleware::getToken();
        require __DIR__ . '/../Views/products/form.php';
    }

    public function store()
    {
        // CSRF manejado por middleware
        try {
            $name = trim($_POST['nombre_producto'] ?? '');
            $categoryId = (int)($_POST['categoria_id'] ?? 0);
            $description = trim($_POST['descripcion'] ?? '');
            $active = isset($_POST['activo']) ? 1 : 0;
            $urlImagen = trim($_POST['url_imagen'] ?? '');

            if ($name === '' || $categoryId <= 0) {
                throw new \InvalidArgumentException('Nombre y categoría son obligatorios');
            }

            $repo = new ProductRepository();
            $id = $repo->create($name, $description, $categoryId, $active, $urlImagen ?: null);
            header('Location: /productos');
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al guardar: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['usuario_id'])) { header('Location: /login'); exit; }
        $user = [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'cajero',
        ];
        $repo = new ProductRepository();
        $product = $repo->findById((int)$id);
        if (!$product) { http_response_code(404); echo 'Producto no encontrado'; return; }
        $categoryRepo = new CategoryRepository();
        $categories = $categoryRepo->findAll();
        $csrf = CsrfMiddleware::getToken();
        require __DIR__ . '/../Views/products/form.php';
    }

    public function update($id)
    {
        try {
            $id = (int)$id;
            $name = trim($_POST['nombre_producto'] ?? '');
            $categoryId = (int)($_POST['categoria_id'] ?? 0);
            $description = trim($_POST['descripcion'] ?? '');
            $active = isset($_POST['activo']) ? 1 : 0;
            $urlImagen = trim($_POST['url_imagen'] ?? '');
            if ($id <= 0 || $name === '' || $categoryId <= 0) {
                throw new \InvalidArgumentException('Datos inválidos');
            }
            $repo = new ProductRepository();
            $repo->update($id, $name, $description, $categoryId, $active, $urlImagen ?: null);
            header('Location: /productos');
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al actualizar: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $productId = (int)$id;
            if ($productId <= 0) {
                throw new \InvalidArgumentException('ID inválido');
            }

            $repo = new ProductRepository();

            try {
                if ($repo->delete($productId)) {
                    header('Location: /productos?ok=1');
                    return;
                }
            } catch (PDOException $e) {
                // continuamos con borrado lógico
            }

            $repo->softDelete($productId);
            header('Location: /productos?info=El%20producto%20se%20desactiv%C3%B3%20porque%20tiene%20ventas%20o%20referencias%20activas');
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al eliminar: ' . htmlspecialchars($e->getMessage());
        }
    }
}
