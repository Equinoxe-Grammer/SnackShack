<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\PaymentMethod;
use PDO;

class PaymentMethodRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    /**
     * @return PaymentMethod[]
     */
    public function findAll(): array
    {
        $sql = 'SELECT metodo_id, nombre_metodo FROM metodos_de_pago ORDER BY nombre_metodo';
        $stmt = $this->db->query($sql);

        $methods = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $methods[] = new PaymentMethod((int) $row['metodo_id'], $row['nombre_metodo'] ?? '');
        }

        return $methods;
    }

    public function exists(int $id): bool
    {
        $sql = 'SELECT COUNT(*) FROM metodos_de_pago WHERE metodo_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }
}
