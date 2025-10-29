<?php
namespace App\Models;

class SaleItem
{
    public ?int $productId;
    public ?string $productName;
    public ?int $variantId;
    public string $variantName;
    public int $quantity;
    public float $unitPrice;
    public float $subtotal;
    public ?int $categoryId;
    public ?string $categoryName;
    public ?float $iva;

    public function __construct(
        ?int $productId,
        ?string $productName,
        ?int $variantId,
        string $variantName,
        int $quantity,
        float $unitPrice,
        float $subtotal,
        ?int $categoryId = null,
        ?string $categoryName = null,
        ?float $iva = 0.0
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->variantId = $variantId;
        $this->variantName = $variantName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->iva = $iva;
    }
}
