<?php
namespace App\Services;

use PDO;
use PDOException;
use RuntimeException;

class CostoService
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Promedio ponderado del costo unitario neto de un ingrediente.
	 * Normaliza compras con IVA (divide costo_total entre 1.15 cuando iva_incluido=1).
	 *
	 * @param int $ingredienteId
	 * @return float
	 * @throws PDOException|RuntimeException
	 */
	public function costoUnitarioIngredienteNeto(int $ingredienteId): float
	{
		$sql = 'SELECT cantidad, costo_total, iva_incluido FROM compras WHERE ingrediente_id = :id';
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt->execute([':id' => $ingredienteId])) {
			throw new PDOException('Error ejecutando consulta de compras');
		}
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (!$rows) {
			throw new RuntimeException("No hay compras para el ingrediente {$ingredienteId}");
		}

		$totalNetCost = 0.0;
		$totalQty = 0.0;

		foreach ($rows as $r) {
			$cantidad = (float)$r['cantidad'];
			$costoTotal = (float)$r['costo_total'];
			$ivaIncluido = (int)$r['iva_incluido'];

			$netCost = $ivaIncluido ? ($costoTotal / 1.15) : $costoTotal;
			$totalNetCost += $netCost;
			$totalQty += $cantidad;
		}

		if ($totalQty <= 0.0) {
			throw new RuntimeException('Cantidad total de compras es cero o inválida');
		}

		return $totalNetCost / $totalQty;
	}

	/**
	 * Costo neto de producción de un producto:
	 * suma de (costo unitario neto * cantidad) / (1 - merma)
	 *
	 * merma se lee desde ingredientes.merma_pct y se interpreta como fracción (ej. 0.10 = 10%).
	 *
	 * @param int $productoId
	 * @return float
	 * @throws PDOException|RuntimeException
	 */
	public function costoProductoNeto(int $productoId): float
	{
		$sql = 'SELECT r.ingrediente_id, r.cantidad as cantidad_req, i.merma_pct
				FROM receta r
				JOIN ingredientes i ON i.id = r.ingrediente_id
				WHERE r.producto_id = :pid';
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt->execute([':pid' => $productoId])) {
			throw new PDOException('Error obteniendo receta');
		}
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (!$rows) {
			throw new RuntimeException("No hay receta para el producto {$productoId}");
		}

		$total = 0.0;
		foreach ($rows as $r) {
			$ingredienteId = (int)$r['ingrediente_id'];
			$cantidadReq = (float)$r['cantidad_req'];
			$merma = (float)$r['merma_pct']; // se asume fracción (0.05 = 5%)

			if ($merma >= 1.0) {
				throw new RuntimeException("Merma inválida para ingrediente {$ingredienteId}");
			}

			$costoUnit = $this->costoUnitarioIngredienteNeto($ingredienteId);

			$factor = (1.0 - $merma);
			if ($factor <= 0.0) {
				throw new RuntimeException("Factor de merma inválido para ingrediente {$ingredienteId}");
			}

			$total += ($costoUnit * $cantidadReq) / $factor;
		}

		return $total;
	}
}

