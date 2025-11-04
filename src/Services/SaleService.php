<?php
namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem as SaleItemModel;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\SaleRepository;
use App\Repositories\VariantRepository;
use InvalidArgumentException;

class SaleService
{
    private SaleRepository $sales;
    private PaymentMethodRepository $paymentMethods;
    private VariantRepository $variants;

    public function __construct(
        ?SaleRepository $sales = null,
        ?PaymentMethodRepository $paymentMethods = null,
        ?VariantRepository $variants = null
    ) {
        $this->sales = $sales ?? new SaleRepository();
        $this->paymentMethods = $paymentMethods ?? new PaymentMethodRepository();
        $this->variants = $variants ?? new VariantRepository();
    }

    public function getDashboardData(?string $date = null): array
    {
        $targetDate = $date ?: date('Y-m-d');
        $totals = $this->sales->getDailyTotals($targetDate);
        // Limitar "Últimas ventas" a la fecha objetivo para evitar confusión
        $recentSales = $this->sales->getRecentSales(5, $targetDate);

        return [
            'totals' => $totals,
            'recent' => $recentSales,
        ];
    }

    public function getHistory(array $filters): array
    {
        $normalized = $this->normalizeFilters($filters);

        $summary = $this->sales->getSummary($normalized);
        $sales = $this->sales->getSales($normalized);

        return [
            'summary' => $summary,
            'sales' => $sales,
        ];
    }

    /**
     * @return Sale[]
     */
    public function getSales(array $filters, int $limit = 200): array
    {
        $normalized = $this->normalizeFilters($filters);

        return $this->sales->getSales($normalized, $limit);
    }

    public function getSalesSummary(array $filters): array
    {
        $normalized = $this->normalizeFilters($filters);

        return $this->sales->getSummary($normalized);
    }

    public function getSaleDetails(int $saleId): ?Sale
    {
        if ($saleId <= 0) {
            throw new InvalidArgumentException('Identificador de venta inválido.');
        }

        return $this->sales->findById($saleId);
    }

    public function registerSale(int $userId, int $paymentMethodId, array $items): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Usuario inválido.');
        }

        if (!$items) {
            throw new InvalidArgumentException('Agrega productos al carrito.');
        }

        if (!$this->paymentMethods->exists($paymentMethodId)) {
            throw new InvalidArgumentException('El método de pago seleccionado no es válido.');
        }

        $normalizedItems = [];
        foreach ($items as $item) {
            $variantId = isset($item['variante_id']) ? (int) $item['variante_id'] : 0;
            $quantity = isset($item['cantidad']) ? (int) $item['cantidad'] : 0;

            if ($variantId <= 0 || $quantity <= 0) {
                continue;
            }

            $normalizedItems[$variantId] = ($normalizedItems[$variantId] ?? 0) + $quantity;
        }

        if (!$normalizedItems) {
            throw new InvalidArgumentException('No se proporcionaron productos válidos.');
        }

        $variantIds = array_keys($normalizedItems);
        $variants = $this->variants->findActiveByIds($variantIds);

        if (count($variants) !== count($variantIds)) {
            throw new InvalidArgumentException('Uno o más productos ya no están disponibles.');
        }

        $saleItems = [];
        $total = 0.0;

        foreach ($normalizedItems as $variantId => $quantity) {
            $variant = $variants[$variantId];
            $unitPrice = $variant->price;
            $subtotal = round($unitPrice * $quantity, 2);

            // Calcular IVA asumido incluido en el precio (15%)
            $unitNet = round($unitPrice / 1.15, 4);
            $unitIva = round($unitPrice - $unitNet, 4);
            $lineIvaTotal = round($unitIva * $quantity, 2);

            $saleItems[] = new SaleItemModel(
                $variant->productId,
                null,
                $variant->id,
                $variant->name,
                $quantity,
                $unitPrice,
                $subtotal,
                null,
                null,
                $lineIvaTotal
            );

            $total += $subtotal;
        }

        $total = round($total, 2);
        $code = $this->generateCode();
        $saleId = $this->sales->createSale($userId, $paymentMethodId, $code, $total, $saleItems);

        return [
            'id' => $saleId,
            'code' => $code,
            'total' => $total,
            'items' => $saleItems,
        ];
    }

    private function generateCode(): string
    {
        return 'V' . date('YmdHis') . strtoupper(bin2hex(random_bytes(2)));
    }

    /**
     * Obtiene el total de ventas de la semana actual (lunes a domingo)
     */
    public function getWeeklySales(): float
    {
        $today = new \DateTime();
        $dayOfWeek = (int) $today->format('N'); // 1 (lunes) a 7 (domingo)
        
        // Calcular el lunes de esta semana
        $mondayThisWeek = clone $today;
        $mondayThisWeek->modify('-' . ($dayOfWeek - 1) . ' days');
        
        $dateFrom = $mondayThisWeek->format('Y-m-d');
        $dateTo = date('Y-m-d');
        
        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
        
        $summary = $this->sales->getSummary($filters);
       return isset($summary['total']) ? (float) $summary['total'] : 0.0;
    }
    
    /**
     * Obtiene las ventas de los últimos 7 días
     * Retorna array con formato: [['fecha' => 'Lun 13', 'total' => 1234.56], ...]
     */
    public function getLast7DaysSales(): array
    {
        $result = [];
        $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = new \DateTime();
            $fecha->modify("-{$i} days");
            $fechaStr = $fecha->format('Y-m-d');
            
            $totals = $this->sales->getDailyTotals($fechaStr);
            $totalVentas = isset($totals['total_sales']) ? (float) $totals['total_sales'] : 0.0;
            
            // Formato: "Lun 13"
            $diaSemana = $diasSemana[(int) $fecha->format('w')];
            $diaNumero = $fecha->format('j');
            $label = "{$diaSemana} {$diaNumero}";
            
            $result[] = [
                'fecha' => $label,
                'total' => $totalVentas,
            ];
        }
        
        return $result;
    }
    
    /**
     * Obtiene la distribución de métodos de pago del día actual
     * Retorna array con formato: [['metodo' => 'Efectivo', 'total' => 1234.56], ...]
     */
    public function getPaymentMethodsDistribution(?string $date = null): array
    {
        return $this->sales->getPaymentMethodsDistribution($date);
    }

    private function normalizeFilters(array $filters): array
    {
        $search = isset($filters['search']) ? trim((string) $filters['search']) : '';
        $date = isset($filters['date']) ? trim((string) $filters['date']) : '';
        $dateFrom = isset($filters['date_from']) ? trim((string) $filters['date_from']) : '';
        $dateTo = isset($filters['date_to']) ? trim((string) $filters['date_to']) : '';

        $category = isset($filters['category']) && $filters['category'] !== ''
            ? (int) $filters['category']
            : null;

        $product = isset($filters['product']) && $filters['product'] !== ''
            ? (int) $filters['product']
            : null;

        $paymentMethod = isset($filters['payment_method']) && $filters['payment_method'] !== ''
            ? (int) $filters['payment_method']
            : null;

        return [
            'search' => $search,
            'date' => $date,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'category' => $category,
            'product' => $product,
            'payment_method' => $paymentMethod,
        ];
    }
}
