<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use App\Database\Connection;

class ProductService
{
    private ProductRepository $products;

    public function __construct(?ProductRepository $products = null)
    {
        $this->products = $products ?? new ProductRepository();
    }

    public function getActiveCatalog(): array
    {
        $productList = $this->products->findActiveProducts();
        $productIds = array_map(static fn($product) => $product->id, $productList);
        $variants = $this->products->findVariantsForProductIds($productIds);
        $this->products->attachVariants($productList, $variants);

        return $productList;
    }

    /**
     * Alias para getActiveCatalog() - Usado por ApiController
     */
    public function getAllWithVariants(): array
    {
        return $this->getActiveCatalog();
    }

    /**
     * Returns products enriched for the index view with pricing/tax/margin info.
     * This centralizes the logic previously done in the controller.
     * Returns ALL products (active and inactive) for admin catalog management.
     *
     * @return array<int, \App\Models\Product>
     */
    public function getProductsForIndexView(): array
    {
        // Obtener TODOS los productos (activos e inactivos) para el catálogo de admin
        $productList = $this->products->findAllProducts();
        $productIds = array_map(static fn($product) => $product->id, $productList);
        $variants = $this->products->findVariantsForProductIds($productIds);
        $this->products->attachVariants($productList, $variants);
        
        $products = $productList;

        // Best-effort cost/tax computation; failures should not break the page
        try {
            $costoService = new \App\Services\CostoService(Connection::get());
            $impuestosService = new \App\Services\ImpuestosService();
        } catch (\Throwable $e) {
            $costoService = null;
            $impuestosService = null;
        }

        foreach ($products as $prod) {
            // Calcular estadísticas agregadas de variantes
            if (!empty($prod->variants)) {
                $prices = array_map(static fn($v) => $v->price, $prod->variants);
                
                // Precio mínimo y máximo
                $prod->precio_min = count($prices) ? round(min($prices), 2) : null;
                $prod->precio_max = count($prices) ? round(max($prices), 2) : null;
                
                // Precio promedio
                $prod->precio_promedio = count($prices) ? round(array_sum($prices) / count($prices), 2) : null;
                
                // Para compatibilidad, precio_final será el mínimo
                $prod->precio_final = $prod->precio_min;
                
                // Calcular promedios de neto e IVA si tenemos impuestosService
                if ($impuestosService && $prod->precio_promedio !== null) {
                    $netosSum = 0;
                    $ivasSum = 0;
                    foreach ($prices as $price) {
                        try {
                            $desglose = $impuestosService->desgloseIVA15((float)$price);
                            $netosSum += $desglose['neto'] ?? 0;
                            $ivasSum += $desglose['iva'] ?? 0;
                        } catch (\Throwable $e) {
                            // Ignorar errores individuales
                        }
                    }
                    $prod->neto_promedio = count($prices) > 0 ? round($netosSum / count($prices), 2) : null;
                    $prod->iva_promedio = count($prices) > 0 ? round($ivasSum / count($prices), 2) : null;
                    
                    // Para compatibilidad, usar valores del precio promedio
                    try {
                        $desglose = $impuestosService->desgloseIVA15((float)$prod->precio_promedio);
                        $prod->neto = $desglose['neto'] ?? null;
                        $prod->iva = $desglose['iva'] ?? null;
                    } catch (\Throwable $e) {
                        $prod->neto = null;
                        $prod->iva = null;
                    }
                } else {
                    $prod->neto_promedio = null;
                    $prod->iva_promedio = null;
                    $prod->neto = null;
                    $prod->iva = null;
                }
            } else {
                $prod->precio_min = null;
                $prod->precio_max = null;
                $prod->precio_promedio = null;
                $prod->precio_final = null;
                $prod->neto_promedio = null;
                $prod->iva_promedio = null;
                $prod->neto = null;
                $prod->iva = null;
            }

            // costo de producción
            $costo = null;
            if ($costoService) {
                try {
                    $c = $costoService->costoProductoNeto($prod->id);
                    $costo = is_numeric($c) ? round((float)$c, 2) : null;
                } catch (\Throwable $e) {
                    $costo = null;
                }
            }
            $prod->costo_produccion = $costo;

            // Margen y margen % basado en precio promedio
            if ($prod->precio_promedio !== null && $prod->costo_produccion !== null) {
                $prod->margen = round($prod->precio_promedio - $prod->costo_produccion, 2);
                $prod->margenPct = $prod->precio_promedio != 0 ? round(($prod->margen / $prod->precio_promedio) * 100, 2) : null;
            } else {
                $prod->margen = null;
                $prod->margenPct = null;
            }
        }

        return $products;
    }
}
