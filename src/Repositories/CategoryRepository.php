<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\Category;
use PDO;

class CategoryRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    /**
     * @return Category[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT categoria_id, nombre_categoria FROM categorias ORDER BY nombre_categoria';
        $stmt = $this->db->query($sql);

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category((int) $row['categoria_id'], $row['nombre_categoria'] ?? '');
        }

        return $categories;
    }
}
