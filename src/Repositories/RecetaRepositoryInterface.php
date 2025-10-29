<?php
namespace App\Repositories;

interface RecetaRepositoryInterface
{
    /**
     * List recipe items by product
     * @param int $productoId
     * @return array
     */
    public function listByProducto(int $productoId): array;

    /**
     * Upsert a recipe item (insert or update)
     * @param int $productoId
     * @param int $ingredienteId
     * @param float $cantidad
     * @return array
     */
    public function upsert(int $productoId, int $ingredienteId, float $cantidad): array;

    /**
     * Delete a recipe item
     * @param int $productoId
     * @param int $ingredienteId
     * @return bool
     */
    public function deleteItem(int $productoId, int $ingredienteId): bool;
}