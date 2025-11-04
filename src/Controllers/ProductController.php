<?php
namespace App\Controllers;

use App\Middleware\CsrfMiddleware;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
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
        $products = $service->getProductsForIndexView();

        $categoryService = new CategoryService();
        $categories = $categoryService->list();

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
    $categoryService = new CategoryService();
    $categories = $categoryService->list();
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
    $categoryService = new CategoryService();
    $categories = $categoryService->list();
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

    public function changeState($id)
    {
        try {
            $productId = (int)$id;
            $newState = (int)($_POST['estado'] ?? 0);
            
            if ($productId <= 0) {
                throw new \InvalidArgumentException('ID inválido');
            }

            $repo = new ProductRepository();
            $repo->changeState($productId, $newState);
            
            $message = $newState === 1 ? 'Producto activado correctamente' : 'Producto desactivado correctamente';
            header('Location: /productos?info=' . urlencode($message));
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al cambiar estado: ' . htmlspecialchars($e->getMessage());
        }
    }
}
