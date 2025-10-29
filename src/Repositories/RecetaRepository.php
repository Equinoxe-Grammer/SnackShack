<?php
namespace App\Repositories;

use PDO;
use PDOException;

class RecetaRepository implements RecetaRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \App\Database\Connection::get();
    }

    public function listByProducto(int $productoId): array
    {
        $sql = 'SELECT r.id, r.producto_id, r.ingrediente_id, i.nombre as ingrediente_nombre, i.unidad_base, r.cantidad
                FROM receta r
                INNER JOIN ingredientes i ON i.id = r.ingrediente_id
                WHERE r.producto_id = ?
                ORDER BY i.nombre';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsert(int $productoId, int $ingredienteId, float $cantidad): array
    {
        $sql = 'INSERT OR REPLACE INTO receta (producto_id, ingrediente_id, cantidad) VALUES (?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productoId, $ingredienteId, $cantidad]);
        // Return the upserted item
        $sql2 = 'SELECT r.id, r.producto_id, r.ingrediente_id, i.nombre as ingrediente_nombre, i.unidad_base, r.cantidad
                 FROM receta r INNER JOIN ingredientes i ON i.id = r.ingrediente_id
                 WHERE r.producto_id = ? AND r.ingrediente_id = ?';
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$productoId, $ingredienteId]);
        return $stmt2->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteItem(int $productoId, int $ingredienteId): bool
    {
        $sql = 'DELETE FROM receta WHERE producto_id = ? AND ingrediente_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productoId, $ingredienteId]);
        return $stmt->rowCount() > 0;
    }
}