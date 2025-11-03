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
     *
     * @return array<int, \App\Models\Product>
     */
    public function getProductsForIndexView(): array
    {
        $products = $this->getActiveCatalog();

        // Best-effort cost/tax computation; failures should not break the page
        try {
            $costoService = new \App\Services\CostoService(Connection::get());
            $impuestosService = new \App\Services\ImpuestosService();
        } catch (\Throwable $e) {
            $costoService = null;
            $impuestosService = null;
        }

        foreach ($products as $prod) {
            // precio_final: mínimo entre variantes activas si existen
            $precioFinal = null;
            if (!empty($prod->variants)) {
                $prices = array_map(static fn($v) => $v->price, $prod->variants);
                $precioFinal = count($prices) ? min($prices) : null;
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

            // Asignar precio_final
            $prod->precio_final = is_numeric($precioFinal) ? round((float)$precioFinal, 2) : null;

            // Desglose IVA
            if ($prod->precio_final !== null && $impuestosService) {
                try {
                    $desglose = $impuestosService->desgloseIVA15((float)$prod->precio_final);
                    $prod->neto = $desglose['neto'] ?? null;
                    $prod->iva = $desglose['iva'] ?? null;
                } catch (\Throwable $e) {
                    $prod->neto = null;
                    $prod->iva = null;
                }
            } else {
                $prod->neto = null;
                $prod->iva = null;
            }

            // Margen y margen %
            if ($prod->precio_final !== null && $prod->costo_produccion !== null) {
                $prod->margen = round($prod->precio_final - $prod->costo_produccion, 2);
                $prod->margenPct = $prod->precio_final != 0 ? round(($prod->margen / $prod->precio_final) * 100, 2) : null;
            } else {
                $prod->margen = null;
                $prod->margenPct = null;
            }
        }

        return $products;
    }
}
