<?php
namespace App\Models;

class Product
{
    public int $id;
    public string $name;
    public ?string $description;
    public ?int $categoryId;
    public ?string $categoryName;
    public ?string $imageUrl;
    public ?bool $hasImage = null;
    public ?string $imageMime = null;
    public ?int $imageSize = null;
    public ?string $imageOriginalName = null;
    public bool $active;

    /** @var Variant[] */
    public array $variants = [];

    // Added properties to avoid creation of dynamic properties (deprecated in PHP 8.2+)
    // These are populated by controllers/services when needed (nullable floats)
    public ?float $costo_produccion = null;
    public ?float $precio_final = null;
    public ?float $neto = null;
    public ?float $iva = null;
    public ?float $margen = null;
    public ?float $margenPct = null;

    public function __construct(
        int $id,
        string $name,
        ?string $description,
        ?int $categoryId,
        ?string $categoryName,
        ?string $imageUrl,
        bool $active,
        ?bool $hasImage = null,
        ?string $imageMime = null,
        ?int $imageSize = null,
        ?string $imageOriginalName = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->imageUrl = $imageUrl;
        $this->active = $active;
        $this->hasImage = $hasImage;
        $this->imageMime = $imageMime;
        $this->imageSize = $imageSize;
        $this->imageOriginalName = $imageOriginalName;
    }

    public function addVariant(Variant $variant): void
    {
        $this->variants[] = $variant;
    }
}
