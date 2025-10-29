<?php
namespace App\Repositories;

interface IngredientesRepositoryInterface
{
    /**
     * Get all ingredients
     * @return array
     */
    public function all(): array;

    /**
     * Find ingredient by ID
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array;

    /**
     * Create a new ingredient
     * @param string $nombre
     * @param string $unidad
     * @param float $mermaPct
     * @return array
     */
    public function create(string $nombre, string $unidad, float $mermaPct): array;

    /**
     * Update an ingredient
     * @param int $id
     * @param string $nombre
     * @param string $unidad
     * @param float $mermaPct
     * @return bool
     */
    public function update(int $id, string $nombre, string $unidad, float $mermaPct): bool;

    /**
     * Delete an ingredient
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}