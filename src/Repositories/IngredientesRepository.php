<?php
namespace App\Repositories;

use PDO;
use PDOException;

class IngredientesRepository implements IngredientesRepositoryInterface
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \App\Database\Connection::get();
    }

    public function all(): array
    {
        $sql = 'SELECT id, nombre, unidad_base, merma_pct FROM ingredientes ORDER BY nombre';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, nombre, unidad_base, merma_pct FROM ingredientes WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(string $nombre, string $unidad, float $mermaPct): array
    {
        $sql = 'INSERT INTO ingredientes (nombre, unidad_base, merma_pct) VALUES (?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre, $unidad, $mermaPct]);
        $id = $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function update(int $id, string $nombre, string $unidad, float $mermaPct): bool
    {
        $sql = 'UPDATE ingredientes SET nombre = ?, unidad_base = ?, merma_pct = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nombre, $unidad, $mermaPct, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM ingredientes WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}