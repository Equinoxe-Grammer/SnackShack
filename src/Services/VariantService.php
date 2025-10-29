<?php
namespace App\Services;

use App\Repositories\VariantRepository;

class VariantService
{
    private VariantRepository $variants;

    public function __construct(?VariantRepository $variants = null)
    {
        $this->variants = $variants ?? new VariantRepository();
    }

    public function listByProduct(int $productId, bool $onlyActive = true): array
    {
        if ($productId <= 0) {
            return [];
        }

        return $onlyActive
            ? $this->variants->findActiveByProduct($productId)
            : $this->variants->findAllByProduct($productId);
    }
}
