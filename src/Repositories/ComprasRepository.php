<?php
namespace App\Repositories;

use PDO;
use PDOException;

class ComprasRepository implements ComprasRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \App\Database\Connection::get();
    }

    public function list(array $filtros = []): array
    {
        $sql = 'SELECT c.id, c.ingrediente_id, i.nombre as ingrediente_nombre, c.cantidad, c.costo_total, c.iva_incluido, c.fecha
                FROM compras c
                INNER JOIN ingredientes i ON i.id = c.ingrediente_id';
        $where = [];
        $params = [];

        if (isset($filtros['ingrediente_id'])) {
            $where[] = 'c.ingrediente_id = ?';
            $params[] = $filtros['ingrediente_id'];
        }
        if (isset($filtros['fecha_from'])) {
            $where[] = 'c.fecha >= ?';
            $params[] = $filtros['fecha_from'];
        }
        if (isset($filtros['fecha_to'])) {
            $where[] = 'c.fecha <= ?';
            $params[] = $filtros['fecha_to'];
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY c.fecha DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $ingredienteId, float $cantidad, float $costoTotal, bool $ivaIncluido = true, ?string $fecha = null): array
    {
        $sql = 'INSERT INTO compras (ingrediente_id, cantidad, costo_total, iva_incluido, fecha) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ingredienteId, $cantidad, $costoTotal, $ivaIncluido ? 1 : 0, $fecha ?? date('Y-m-d H:i:s')]);
        $id = $this->db->lastInsertId();
        // Return the created record
        $sql2 = 'SELECT c.id, c.ingrediente_id, i.nombre as ingrediente_nombre, c.cantidad, c.costo_total, c.iva_incluido, c.fecha
                 FROM compras c INNER JOIN ingredientes i ON i.id = c.ingrediente_id WHERE c.id = ?';
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$id]);
        return $stmt2->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM compras WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}