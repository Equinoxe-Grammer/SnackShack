<?php
namespace App\Services;

use App\Repositories\VariantRepository;
use InvalidArgumentException;

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

    /** Creates a variant for a product with basic validation */
    public function create(int $productId, string $name, float $price, ?float $volumeOunces, bool $active = true): int
    {
        $productId = (int)$productId;
        $name = trim($name);
        $price = (float)$price;
        $volumeOunces = $volumeOunces !== null ? (float)$volumeOunces : null;
        if ($productId <= 0 || $name === '' || $price <= 0) {
            throw new InvalidArgumentException('Nombre, precio y producto son obligatorios');
        }
        return $this->variants->create($productId, $name, $price, $volumeOunces, $active ? 1 : 0);
    }

    /** Updates an existing variant */
    public function update(int $variantId, string $name, float $price, ?float $volumeOunces, bool $active = true): bool
    {
        $variantId = (int)$variantId;
        $name = trim($name);
        $price = (float)$price;
        $volumeOunces = $volumeOunces !== null ? (float)$volumeOunces : null;
        if ($variantId <= 0 || $name === '' || $price <= 0) {
            throw new InvalidArgumentException('Nombre y precio son obligatorios');
        }
        return $this->variants->update($variantId, $name, $price, $volumeOunces, $active ? 1 : 0);
    }

    /** Changes active state */
    public function setActive(int $variantId, bool $active): bool
    {
        $variantId = (int)$variantId;
        if ($variantId <= 0) {
            throw new InvalidArgumentException('ID de variante inválido');
        }
        return $this->variants->setActive($variantId, $active);
    }

    /** Deletes variant or returns false if hard delete not possible */
    public function delete(int $variantId): bool
    {
        $variantId = (int)$variantId;
        if ($variantId <= 0) {
            throw new InvalidArgumentException('ID de variante inválido');
        }
        return $this->variants->delete($variantId);
    }

    public function softDelete(int $variantId): bool
    {
        $variantId = (int)$variantId;
        if ($variantId <= 0) {
            throw new InvalidArgumentException('ID de variante inválido');
        }
        return $this->variants->softDelete($variantId);
    }
}
