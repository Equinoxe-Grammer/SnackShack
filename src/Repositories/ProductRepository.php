<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\Product;
use App\Models\Variant;
use PDO;
use PDOException;

class ProductRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    /**
     * @return Product[]
     */
    public function findActiveProducts(): array
    {
        $sql = 'SELECT
                    p.producto_id,
                    p.nombre_producto,
                    p.descripcion,
                    p.categoria_id,
                    c.nombre_categoria,
                    p.url_imagen,
                    CASE WHEN p.imagen IS NOT NULL AND length(p.imagen) > 0 THEN 1 ELSE 0 END AS has_image,
                    p.imagen_mime,
                    p.imagen_size,
                    p.imagen_nombre,
                    p.activo
                FROM productos p
                INNER JOIN categorias c ON c.categoria_id = p.categoria_id
                WHERE p.activo = 1
                ORDER BY p.nombre_producto';

        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($rows as $row) {
            $products[] = new Product(
                (int) $row['producto_id'],
                $row['nombre_producto'] ?? '',
                $row['descripcion'] ?? '',
                isset($row['categoria_id']) ? (int) $row['categoria_id'] : null,
                $row['nombre_categoria'] ?? null,
                $row['url_imagen'] ?? null,
                (int) $row['activo'] === 1,
                isset($row['has_image']) ? ((int)$row['has_image'] === 1) : null,
                $row['imagen_mime'] ?? null,
                isset($row['imagen_size']) ? (int)$row['imagen_size'] : null,
                $row['imagen_nombre'] ?? null
            );
        }

        return $products;
    }

    /**
     * @return Variant[]
     */
    public function findVariantsForProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT
                    v.variante_id,
                    v.producto_id,
                    v.nombre_variante,
                    v.volumen_onzas,
                    v.precio,
                    v.activo
                FROM variantes v
                WHERE v.producto_id IN ($placeholders)
                  AND v.activo = 1
                ORDER BY v.volumen_onzas, v.nombre_variante";

        $stmt = $this->db->prepare($sql);
        foreach ($productIds as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
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

    public function attachVariants(array $products, array $variants): array
    {
        $variantsByProduct = [];
        foreach ($variants as $variant) {
            $variantsByProduct[$variant->productId][] = $variant;
        }

        foreach ($products as $product) {
            if (!empty($variantsByProduct[$product->id])) {
                $product->variants = $variantsByProduct[$product->id];
            }
        }

        return $products;
    }

    // ===== CRUD Básico =====
    public function create(string $name, ?string $description, int $categoryId, int $active, ?string $imageUrl): int
    {
        $sql = 'INSERT INTO productos (nombre_producto, descripcion, categoria_id, activo, url_imagen)
                VALUES (:name, :desc, :cat, :act, :url)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':desc' => $description,
            ':cat' => $categoryId,
            ':act' => $active,
            ':url' => $imageUrl,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?Product
    {
        $sql = 'SELECT p.producto_id, p.nombre_producto, p.descripcion, p.categoria_id, c.nombre_categoria,
                       p.url_imagen, p.activo,
                       CASE WHEN p.imagen IS NOT NULL AND length(p.imagen) > 0 THEN 1 ELSE 0 END AS has_image,
                       p.imagen_mime, p.imagen_size, p.imagen_nombre
                FROM productos p INNER JOIN categorias c ON c.categoria_id = p.categoria_id
                WHERE p.producto_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Product(
            (int)$row['producto_id'],
            $row['nombre_producto'] ?? '',
            $row['descripcion'] ?? '',
            isset($row['categoria_id']) ? (int)$row['categoria_id'] : null,
            $row['nombre_categoria'] ?? null,
            $row['url_imagen'] ?? null,
            (int)$row['activo'] === 1,
            isset($row['has_image']) ? ((int)$row['has_image'] === 1) : null,
            $row['imagen_mime'] ?? null,
            isset($row['imagen_size']) ? (int)$row['imagen_size'] : null,
            $row['imagen_nombre'] ?? null
        );
    }

    public function update(int $id, string $name, ?string $description, int $categoryId, int $active, ?string $imageUrl): bool
    {
        $sql = 'UPDATE productos SET nombre_producto = :name, descripcion = :desc, categoria_id = :cat, activo = :act, url_imagen = :url
                WHERE producto_id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':desc' => $description,
            ':cat' => $categoryId,
            ':act' => $active,
            ':url' => $imageUrl,
        ]);
    }

    public function softDelete(int $id): bool
    {
        $sql = 'UPDATE productos SET activo = 0 WHERE producto_id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            $variantStmt = $this->db->prepare('DELETE FROM variantes WHERE producto_id = :id');
            $variantStmt->bindValue(':id', $id, PDO::PARAM_INT);
            $variantStmt->execute();

            $productStmt = $this->db->prepare('DELETE FROM productos WHERE producto_id = :id');
            $productStmt->bindValue(':id', $id, PDO::PARAM_INT);
            $productStmt->execute();

            if ($productStmt->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene metadata de imagen para un producto
     */
    public function getImageMeta(int $productId): ?array
    {
        $sql = 'SELECT imagen_mime, imagen_size, imagen_nombre,
                       CASE WHEN imagen IS NOT NULL AND length(imagen) > 0 THEN 1 ELSE 0 END AS has_image
                FROM productos WHERE producto_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Obtiene el BLOB de imagen y mime type
     */
    public function getImageBlob(int $productId): ?array
    {
        $sql = 'SELECT imagen, imagen_mime FROM productos WHERE producto_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || empty($row['imagen'])) {
            return null;
        }
        return [
            'data' => $row['imagen'],
            'mime' => $row['imagen_mime'] ?: 'application/octet-stream',
        ];
    }

    /**
     * Actualiza BLOB de imagen y metadata
     */
    public function updateImage(int $productId, string $blob, string $mime, string $originalName, int $size): bool
    {
        try {
            $sql = 'UPDATE productos SET imagen = :img, imagen_mime = :mime, imagen_nombre = :name, imagen_size = :size WHERE producto_id = :id';
            $stmt = $this->db->prepare($sql);

            // En SQLite es recomendable usar PARAM_LOB para BLOBs
            $stmt->bindParam(':img', $blob, PDO::PARAM_LOB);
            $stmt->bindValue(':mime', $mime, PDO::PARAM_STR);
            $stmt->bindValue(':name', $originalName, PDO::PARAM_STR);
            $stmt->bindValue(':size', $size, PDO::PARAM_INT);
            $stmt->bindValue(':id', $productId, PDO::PARAM_INT);

            $result = $stmt->execute();
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL updateImage: " . print_r($errorInfo, true));
                return false;
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log("PDO Error en updateImage: " . $e->getMessage());
            throw $e;
        }
    }
}
