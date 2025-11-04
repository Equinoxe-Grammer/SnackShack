<?php
namespace App\Repositories;

use App\Database\Connection;
use App\Models\Sale;
use App\Models\SaleItem;
use PDO;
use Throwable;

class SaleRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::get();
    }

    public function getDailyTotals(string $date): array
    {
        error_log("SaleRepository::getDailyTotals - Buscando ventas para fecha: " . $date);
        
                $sql = "SELECT
                                        COALESCE(SUM(total), 0) AS total_sales,
                                        COUNT(*) AS transactions,
                                        COALESCE(AVG(total), 0) AS average_sale
                                FROM ventas
                                WHERE date(fecha_hora) = :date
                                    AND estado = 'pagada'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
        error_log("SaleRepository::getDailyTotals - Resultados: " . print_r($row, true));

        return [
            'total_sales' => isset($row['total_sales']) ? (float) $row['total_sales'] : 0.0,
            'transactions' => isset($row['transactions']) ? (int) $row['transactions'] : 0,
            'average_sale' => isset($row['average_sale']) ? (float) $row['average_sale'] : 0.0,
        ];
    }

    /**
     * Obtiene popularidad por producto (unidades vendidas) en los últimos N días.
     * Retorna un arreglo [producto_id => unidades_vendidas]
     */
    public function getProductPopularity(int $days = 30): array
    {
        // Proteger valor mínimo
        if ($days < 1) { $days = 1; }

                $sql = "SELECT
                                        va.producto_id AS producto_id,
                                        COALESCE(SUM(dv.cantidad), 0) AS unidades
                FROM ventas v
                INNER JOIN detalle_ventas dv ON dv.venta_id = v.venta_id
                INNER JOIN variantes va ON va.variante_id = dv.variante_id
                WHERE v.estado = 'pagada'
                                    AND v.fecha_hora >= datetime('now', '-' || :days || ' days')
                GROUP BY va.producto_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        $popularity = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $popularity[(int)$row['producto_id']] = (int)$row['unidades'];
        }

        return $popularity;
    }

    /**
     * @return Sale[]
     */
    public function getRecentSales(int $limit = 5, ?string $date = null): array
    {
        $sql = "SELECT
                    v.venta_id,
                    v.codigo,
                    date(v.fecha_hora) AS fecha,
                    strftime('%H:%M', v.fecha_hora) AS hora,
                    u.usuario,
                    m.nombre_metodo,
                    v.total
                FROM ventas v
                INNER JOIN usuarios u ON u.usuario_id = v.usuario_id
                INNER JOIN metodos_de_pago m ON m.metodo_id = v.metodo_id
                WHERE v.estado = 'pagada'";

        // Filtrar por fecha específica si se indica (YYYY-MM-DD)
        $params = [];
        if ($date) {
            $sql .= " AND date(v.fecha_hora) = :date";
            $params[':date'] = $date;
        }

        $sql .= "
                ORDER BY v.fecha_hora DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $sales = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sale = new Sale(
                (int) $row['venta_id'],
                $row['codigo'] ?? '',
                $row['fecha'] ?? '',
                $row['hora'] ?? '',
                $row['usuario'] ?? '',
                $row['nombre_metodo'] ?? '',
                (float) $row['total']
            );
            $sales[] = $sale;
        }

        return $sales;
    }

    public function getSummary(array $filters): array
    {
        $where = ["v.estado = 'pagada'"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(v.codigo LIKE :search OR u.usuario LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date'])) {
            $where[] = 'DATE(v.fecha_hora) = :date';
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(v.fecha_hora) >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(v.fecha_hora) <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['category'])) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM detalle_ventas dv
                INNER JOIN variantes va ON va.variante_id = dv.variante_id
                INNER JOIN productos p ON p.producto_id = va.producto_id
                WHERE dv.venta_id = v.venta_id AND p.categoria_id = :category
            )';
            $params[':category'] = (int) $filters['category'];
        }

        if (!empty($filters['product'])) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM detalle_ventas dv2
                INNER JOIN variantes va2 ON va2.variante_id = dv2.variante_id
                WHERE dv2.venta_id = v.venta_id AND va2.producto_id = :product
            )';
            $params[':product'] = (int) $filters['product'];
        }

        if (!empty($filters['payment_method'])) {
            $where[] = 'v.metodo_id = :payment_method';
            $params[':payment_method'] = (int) $filters['payment_method'];
        }

        $sql = 'SELECT
                    COUNT(*) AS total_sales,
                    COALESCE(SUM(v.total), 0) AS total_amount,
                    COALESCE(AVG(v.total), 0) AS average_amount
                FROM ventas v
                INNER JOIN usuarios u ON u.usuario_id = v.usuario_id
                ' . ($where ? 'WHERE ' . implode(' AND ', $where) : '');

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, [':category', ':product', ':payment_method'], true)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'sales' => isset($row['total_sales']) ? (int) $row['total_sales'] : 0,
            'total' => isset($row['total_amount']) ? (float) $row['total_amount'] : 0.0,
            'average' => isset($row['average_amount']) ? (float) $row['average_amount'] : 0.0,
        ];
    }

    /**
     * @return Sale[]
     */
    public function getSales(array $filters, int $limit = 200): array
    {
        $where = ["v.estado = 'pagada'"];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(v.codigo LIKE :search OR u.usuario LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date'])) {
            $where[] = 'date(v.fecha_hora) = :date';
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = 'date(v.fecha_hora) >= :date_from';
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'date(v.fecha_hora) <= :date_to';
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['category'])) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM detalle_ventas dv
                INNER JOIN variantes va ON va.variante_id = dv.variante_id
                INNER JOIN productos p ON p.producto_id = va.producto_id
                WHERE dv.venta_id = v.venta_id AND p.categoria_id = :category
            )';
            $params[':category'] = (int) $filters['category'];
        }

        if (!empty($filters['product'])) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM detalle_ventas dv2
                INNER JOIN variantes va2 ON va2.variante_id = dv2.variante_id
                WHERE dv2.venta_id = v.venta_id AND va2.producto_id = :product
            )';
            $params[':product'] = (int) $filters['product'];
        }

        if (!empty($filters['payment_method'])) {
            $where[] = 'v.metodo_id = :payment_method';
            $params[':payment_method'] = (int) $filters['payment_method'];
        }

        $sql = 'SELECT
                    v.venta_id,
                    v.codigo,
                    date(v.fecha_hora) AS fecha,
                    strftime("%H:%M", v.fecha_hora) AS hora,
                    u.usuario,
                    m.nombre_metodo,
                    v.total
                FROM ventas v
                INNER JOIN usuarios u ON u.usuario_id = v.usuario_id
                INNER JOIN metodos_de_pago m ON m.metodo_id = v.metodo_id
                ' . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . '
                ORDER BY v.fecha_hora DESC
                LIMIT :limit';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if (in_array($key, [':category', ':product', ':payment_method'], true)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $sales = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sale = new Sale(
                (int) $row['venta_id'],
                $row['codigo'] ?? '',
                $row['fecha'] ?? '',
                $row['hora'] ?? '',
                $row['usuario'] ?? '',
                $row['nombre_metodo'] ?? '',
                (float) $row['total']
            );
            $sales[$sale->id] = $sale;
        }

        if (!$sales) {
            return [];
        }

        $this->attachSaleItems($sales);

        return array_values($sales);
    }

    public function findById(int $saleId): ?Sale
    {
                        $sql = "SELECT
                                                v.venta_id,
                                                v.codigo,
                                                date(v.fecha_hora) AS fecha,
                                                strftime('%H:%M', v.fecha_hora) AS hora,
                                                u.usuario,
                                                m.nombre_metodo,
                                                v.total
                                        FROM ventas v
                                        INNER JOIN usuarios u ON u.usuario_id = v.usuario_id
                                        INNER JOIN metodos_de_pago m ON m.metodo_id = v.metodo_id
                                        WHERE v.venta_id = :venta
                                            AND v.estado = 'pagada'
                                        LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':venta', $saleId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $sale = new Sale(
            (int) $row['venta_id'],
            $row['codigo'] ?? '',
            $row['fecha'] ?? '',
            $row['hora'] ?? '',
            $row['usuario'] ?? '',
            $row['nombre_metodo'] ?? '',
            (float) $row['total']
        );

        $sales = [$sale->id => $sale];
        $this->attachSaleItems($sales);

        return $sales[$sale->id] ?? null;
    }

    /**
     * @param array<int, Sale> $salesById
     */
    private function attachSaleItems(array &$salesById): void
    {
        $ids = array_keys($salesById);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT
                    dv.venta_id,
                    dv.variante_id,
                    va.nombre_variante,
                    va.producto_id,
                    p.nombre_producto,
                    c.categoria_id,
                    c.nombre_categoria,
                    dv.cantidad,
                    dv.precio_unitario,
                    dv.sub_total
                FROM detalle_ventas dv
                INNER JOIN variantes va ON va.variante_id = dv.variante_id
                INNER JOIN productos p ON p.producto_id = va.producto_id
                INNER JOIN categorias c ON c.categoria_id = p.categoria_id
                WHERE dv.venta_id IN ($placeholders)
                ORDER BY dv.venta_id";

        $stmt = $this->db->prepare($sql);
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $saleId = (int) $row['venta_id'];
            if (!isset($salesById[$saleId])) {
                continue;
            }

            $item = new SaleItem(
                isset($row['producto_id']) ? (int) $row['producto_id'] : null,
                $row['nombre_producto'] ?? null,
                isset($row['variante_id']) ? (int) $row['variante_id'] : null,
                $row['nombre_variante'] ?? '',
                (int) $row['cantidad'],
                (float) $row['precio_unitario'],
                (float) $row['sub_total'],
                isset($row['categoria_id']) ? (int) $row['categoria_id'] : null,
                $row['nombre_categoria'] ?? null
            );

            $salesById[$saleId]->addItem($item);
        }
    }

    /**
     * @param array<int, SaleItem> $items
     */
    public function createSale(int $userId, int $paymentMethodId, string $code, float $total, array $items): int
    {
        $this->db->beginTransaction();

        try {
            // Obtener fecha/hora actual en zona horaria America/Mexico_City
            $dt = new \DateTime('now', new \DateTimeZone('America/Mexico_City'));
            $fechaHora = $dt->format('Y-m-d H:i:s');

            $stmt = $this->db->prepare(
                "INSERT INTO ventas (codigo, usuario_id, metodo_id, total, estado, fecha_hora)
                 VALUES (:codigo, :usuario, :metodo, :total, 'pagada', :fecha_hora)"
            );
            $stmt->execute([
                ':codigo' => $code,
                ':usuario' => $userId,
                ':metodo' => $paymentMethodId,
                ':total' => $total,
                ':fecha_hora' => $fechaHora,
            ]);

            $saleId = (int) $this->db->lastInsertId();

            $detailStmt = $this->db->prepare(
                'INSERT INTO detalle_ventas (venta_id, variante_id, cantidad, precio_unitario, iva, sub_total)
                 VALUES (:venta, :variante, :cantidad, :precio, :iva, :subtotal)'
            );

            foreach ($items as $item) {
                $detailStmt->execute([
                    ':venta' => $saleId,
                    ':variante' => $item->variantId,
                    ':cantidad' => $item->quantity,
                    ':precio' => $item->unitPrice,
                    ':iva' => isset($item->iva) ? $item->iva : 0.00,
                    ':subtotal' => $item->subtotal,
                ]);
            }

            $this->db->commit();

            return $saleId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene la distribución de métodos de pago para una fecha específica
     * @param string|null $date Fecha en formato Y-m-d (por defecto hoy)
     * @return array Array de ['metodo' => string, 'total' => float]
     */
    public function getPaymentMethodsDistribution(?string $date = null): array
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $sql = "
            SELECT 
                mp.nombre_metodo AS metodo,
                COALESCE(SUM(v.total), 0) AS total
            FROM metodos_de_pago mp
            LEFT JOIN ventas v ON v.metodo_id = mp.metodo_id 
                AND date(v.fecha_hora) = :date
                AND v.estado = 'pagada'
            GROUP BY mp.metodo_id, mp.nombre_metodo
            HAVING total > 0
            ORDER BY total DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(function($row) {
            return [
                'metodo' => $row['metodo'],
                'total' => (float) $row['total'],
            ];
        }, $results);
    }
}
