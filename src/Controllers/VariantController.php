<?php
namespace App\Controllers;

use App\Middleware\CsrfMiddleware;
use App\Repositories\VariantRepository;
use App\Services\VariantService;

class VariantController
{
    public function index($id)
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
        $productId = (int)$id;
        $service = new VariantService();
        $variants = $service->listByProduct($productId, false);
        $csrf = CsrfMiddleware::getToken();
        $productId = (int)$id;
        require __DIR__ . '/../Views/products/variants.php';
    }

    public function create($id)
    {
        if (!isset($_SESSION['usuario_id'])) { header('Location: /login'); exit; }
        $csrf = CsrfMiddleware::getToken();
        $productId = (int)$id;
        $variant = null;
        require __DIR__ . '/../Views/products/variant_form.php';
    }

    public function store($id)
    {
        try {
            $productId = (int)$id;
            $name = trim($_POST['nombre_variante'] ?? '');
            $price = (float)($_POST['precio'] ?? 0);
            $volume = isset($_POST['volumen_onzas']) ? (float)$_POST['volumen_onzas'] : null;
            $active = isset($_POST['activo']) ? 1 : 0;
            if ($name === '' || $price <= 0) {
                throw new \InvalidArgumentException('Nombre y precio son obligatorios');
            }
            $repo = new VariantRepository();
            $repo->create($productId, $name, $price, $volume, $active);
            header('Location: /productos/' . $productId . '/variantes');
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al guardar: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function edit($id, $vid)
    {
        if (!isset($_SESSION['usuario_id'])) { header('Location: /login'); exit; }
        $repo = new VariantRepository();
        $productId = (int)$id;
        $variant = $repo->findById((int)$vid);
        if (!$variant) { http_response_code(404); echo 'Variante no encontrada'; return; }
        $csrf = CsrfMiddleware::getToken();
        require __DIR__ . '/../Views/products/variant_form.php';
    }

    public function update($id, $vid)
    {
        try {
            $productId = (int)$id;
            $name = trim($_POST['nombre_variante'] ?? '');
            $price = (float)($_POST['precio'] ?? 0);
            $volume = isset($_POST['volumen_onzas']) ? (float)$_POST['volumen_onzas'] : null;
            $active = isset($_POST['activo']) ? 1 : 0;
            if ($name === '' || $price <= 0) {
                throw new \InvalidArgumentException('Nombre y precio son obligatorios');
            }
            $repo = new VariantRepository();
            $repo->update((int)$vid, $name, $price, $volume, $active);
            header('Location: /productos/' . $productId . '/variantes');
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Error al actualizar: ' . htmlspecialchars($e->getMessage());
        }
    }

    public function changeState($id, $vid)
    {
        try {
            $productId = (int)$id;
            $newState = ($_POST['estado'] ?? '') === '1';
            $repo = new VariantRepository();
            if (!$repo->setActive((int)$vid, $newState)) {
                throw new \RuntimeException('No fue posible actualizar el estado de la variante');
            }
            header('Location: /productos/' . $productId . '/variantes?ok=1');
        } catch (\Throwable $e) {
            http_response_code(400);
            $message = urlencode($e->getMessage());
            header('Location: /productos/' . (int)$id . '/variantes?err=' . $message);
        }
    }

    public function delete($id, $vid)
    {
        try {
            $repo = new VariantRepository();
            $productId = (int)$id;
            $variantId = (int)$vid;
            $deleted = $repo->delete($variantId);
            if (!$deleted) {
                $repo->softDelete($variantId);
                header('Location: /productos/' . $productId . '/variantes?info=La%20variante%20se%20desactiv%C3%B3%20porque%20tiene%20ventas%20registradas');
                return;
            }
            header('Location: /productos/' . $productId . '/variantes?ok=1');
        } catch (\Throwable $e) {
            http_response_code(400);
            $message = urlencode('No se pudo eliminar la variante: ' . $e->getMessage());
            header('Location: /productos/' . (int)$id . '/variantes?err=' . $message);
        }
    }
}
