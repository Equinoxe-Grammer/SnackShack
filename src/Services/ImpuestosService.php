<?php
namespace App\Services;

class ImpuestosService
{
	/**
	 * Desglose de IVA 15% sobre un precio final (total).
	 * Retorna array con keys: neto, iva, total
	 *
	 * @param float $precioFinal
	 * @return array{neto:float,iva:float,total:float}
	 */
	public function desgloseIVA15(float $precioFinal): array
	{
		$total = round($precioFinal, 2);
		$neto = round($total / 1.15, 2);
		$iva = round($total - $neto, 2);

		return [
			'neto' => $neto,
			'iva' => $iva,
			'total' => $total,
		];
	}
}

