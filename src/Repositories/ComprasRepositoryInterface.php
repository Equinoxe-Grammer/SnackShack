<?php
namespace App\Repositories;

interface ComprasRepositoryInterface
{
    /**
     * List purchases with filters
     * @param array $filtros
     * @return array
     */
    public function list(array $filtros = []): array;

    /**
     * Create a new purchase
     * @param int $ingredienteId
     * @param float $cantidad
     * @param float $costoTotal
     * @param bool $ivaIncluido
     * @param string|null $fecha
     * @return array
     */
    public function create(int $ingredienteId, float $cantidad, float $costoTotal, bool $ivaIncluido = true, ?string $fecha = null): array;

    /**
     * Delete a purchase
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}