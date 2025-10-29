<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\Variant;
use PDO;

class VariantRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    /**
     * @return array<int, Variant>
     */
    public function findActiveByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT
                    variante_id,
                    producto_id,
                    nombre_variante,
                    precio,
                    volumen_onzas,
                    activo
                FROM variantes
                WHERE variante_id IN ($placeholders)
                  AND activo = 1";

        $stmt = $this->db->prepare($sql);
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        $variants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $variantId = (int) $row['variante_id'];
            $variants[$variantId] = new Variant(
                $variantId,
                (int) $row['producto_id'],
                $row['nombre_variante'] ?? '',
                (float) $row['precio'],
                isset($row['volumen_onzas']) ? (float) $row['volumen_onzas'] : null,
                (int) $row['activo'] === 1
            );
        }

        return $variants;
    }

    /**
     * @return Variant[]
     */
    public function findActiveByProduct(int $productId): array
    {
        $sql = 'SELECT
                    variante_id,
                    producto_id,
                    nombre_variante,
                    precio,
                    volumen_onzas,
                    activo
                FROM variantes
                WHERE producto_id = :product
                  AND activo = 1
                ORDER BY nombre_variante';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product', $productId, PDO::PARAM_INT);
        $stmt->execute();

        $variants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $variants[] = new Variant(
                (int) $row['variante_id'],
                (int) $row['producto_id'],
                $row['nombre_variante'] ?? '',
                (float) $row['precio'],
                isset($row['volumen_onzas']) ? (float) $row['volumen_onzas'] : null,
                (int) $row['activo'] === 1
            );
        }

        return $variants;
    }

    /**
     * @return Variant[]
     */
    public function findAllByProduct(int $productId): array
    {
        $sql = 'SELECT
                    variante_id,
                    producto_id,
                    nombre_variante,
                    precio,
                    volumen_onzas,
                    activo
                FROM variantes
                WHERE producto_id = :product
                ORDER BY activo DESC, nombre_variante';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product', $productId, PDO::PARAM_INT);
        $stmt->execute();

        $variants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $variants[] = new Variant(
                (int) $row['variante_id'],
                (int) $row['producto_id'],
                $row['nombre_variante'] ?? '',
                (float) $row['precio'],
                isset($row['volumen_onzas']) ? (float) $row['volumen_onzas'] : null,
                (int) $row['activo'] === 1
            );
        }

        return $variants;
    }

    /**
     * Crea una nueva variante para un producto
     */
    public function create(int $productId, string $name, float $price, ?float $volumeOunces, int $active = 1): int
    {
        $sql = 'INSERT INTO variantes (producto_id, nombre_variante, precio, volumen_onzas, activo)
                VALUES (:productId, :name, :price, :volume, :active)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':volume', $volumeOunces);
        $stmt->bindValue(':active', $active, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->db->lastInsertId();
    }

    /**
     * Busca una variante por su ID
     */
    public function findById(int $variantId): ?Variant
    {
        $sql = 'SELECT variante_id, producto_id, nombre_variante, precio, volumen_onzas, activo
                FROM variantes WHERE variante_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $variantId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Variant(
            (int)$row['variante_id'],
            (int)$row['producto_id'],
            $row['nombre_variante'] ?? '',
            (float)$row['precio'],
            isset($row['volumen_onzas']) ? (float)$row['volumen_onzas'] : null,
            (int)$row['activo'] === 1
        );
    }

    /**
     * Actualiza una variante existente
     */
    public function update(int $variantId, string $name, float $price, ?float $volumeOunces, int $active = 1): bool
    {
        $sql = 'UPDATE variantes SET nombre_variante = :name, precio = :price, volumen_onzas = :volume, activo = :active
                WHERE variante_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $variantId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':volume', $volumeOunces);
        $stmt->bindValue(':active', $active, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cambia el estado activo de una variante sin modificar otros campos
     */
    public function setActive(int $variantId, bool $active): bool
    {
        $sql = 'UPDATE variantes SET activo = :active WHERE variante_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $variantId, PDO::PARAM_INT);
        $stmt->bindValue(':active', $active ? 1 : 0, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Borrado lógico de variante
     */
    public function softDelete(int $variantId): bool
    {
        $sql = 'UPDATE variantes SET activo = 0 WHERE variante_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $variantId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $variantId): bool
    {
        $sql = 'DELETE FROM variantes WHERE variante_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $variantId, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->rowCount() > 0;
    }
}
