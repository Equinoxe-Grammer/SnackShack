<?php
namespace App\Services;

use App\Repositories\ProductRepository;

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
}
