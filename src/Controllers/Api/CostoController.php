<?php
namespace App\Controllers\Api;

use App\Database\Connection;
use App\Services\CostoService;
use App\Services\ImpuestosService;
use PDO;

class CostoController
{
    private PDO $db;
    private CostoService $costoService;
    private ImpuestosService $impuestosService;

    public function __construct()
    {
        $this->db = Connection::get();
        $this->costoService = new CostoService($this->db);
        $this->impuestosService = new ImpuestosService();
    }

    // GET /api/productos/{id}/costo
    public function productoCosto($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        $productoId = (int)$id;
        if ($productoId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        try {
            // Obtener precio_final desde productos (si existe). Si la columna no existe,
            // hacer fallback a la variante mínima para definir un precio_final.
            $precioFinal = null;
            try {
                $stmt = $this->db->prepare('SELECT producto_id, nombre_producto, precio_final FROM productos WHERE producto_id = :id');
                $stmt->execute([':id' => $productoId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Producto no encontrado']);
                    return;
                }
                $precioFinal = isset($row['precio_final']) ? (float)$row['precio_final'] : null;
            } catch (\Throwable $e) {
                // Posible que la columna precio_final no exista; fallback a variante mínima
                try {
                    $stmt2 = $this->db->prepare('SELECT MIN(precio) as precio_min FROM variantes WHERE producto_id = :id');
                    $stmt2->execute([':id' => $productoId]);
                    $r2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    $precioFinal = $r2 ? (isset($r2['precio_min']) ? (float)$r2['precio_min'] : null) : null;
                } catch (\Throwable $inner) {
                    throw $e; // rethrow original
                }
            }

            // Calcular costo neto de producción (si no hay receta, dejar null en lugar de fallar)
            try {
                $costoNeto = $this->costoService->costoProductoNeto($productoId);
            } catch (\Throwable $e) {
                $costoNeto = null;
            }

            // Desglose IVA usando precio_final si está disponible, si no, usar costo+margen?
            if ($precioFinal === null) {
                // Si no hay precio_final, respondemos con costo_neto y nulls
                $payload = [
                    'costo_neto' => round($costoNeto, 2),
                    'precio_final' => null,
                    'neto' => null,
                    'iva' => null,
                    'margen' => null,
                    'margenPct' => null,
                ];
                echo json_encode($payload);
                return;
            }

            $desglose = $this->impuestosService->desgloseIVA15($precioFinal);

            if (is_null($costoNeto)) {
                $margen = null;
                $margenPct = null;
                $costoNetoPayload = null;
            } else {
                $margen = round($precioFinal - $costoNeto, 2);
                $margenPct = $precioFinal != 0 ? round(($margen / $precioFinal) * 100, 2) : null;
                $costoNetoPayload = round($costoNeto, 2);
            }

            $payload = [
                'costo_neto' => $costoNetoPayload,
                'precio_final' => round($precioFinal, 2),
                'neto' => $desglose['neto'],
                'iva' => $desglose['iva'],
                'margen' => $margen,
                'margenPct' => $margenPct,
            ];

            echo json_encode($payload);
        } catch (\Throwable $e) {
            error_log('Error en productoCosto: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error calculando costo: ' . $e->getMessage()]);
        }
    }

    // GET /api/ingredientes/costos
    public function ingredientesCostos()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $stmt = $this->db->query('SELECT id, nombre FROM ingredientes ORDER BY nombre');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($rows as $r) {
                $id = (int)$r['id'];
                $nombre = $r['nombre'];
                try {
                    $costo = $this->costoService->costoUnitarioIngredienteNeto($id);
                    $unit = round($costo, 4);
                } catch (\Throwable $e) {
                    $unit = null; // sin compras
                }
                $result[] = [
                    'ingrediente_id' => $id,
                    'nombre' => $nombre,
                    'costo_unit_neto' => $unit,
                ];
            }

            echo json_encode($result);
        } catch (\Throwable $e) {
            error_log('Error en ingredientesCostos: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener costos de ingredientes']);
        }
    }
}
