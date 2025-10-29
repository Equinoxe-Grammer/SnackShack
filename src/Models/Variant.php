<?php
namespace App\Models;

class Variant
{
    public int $id;
    public int $productId;
    public string $name;
    public float $price;
    public ?float $volumeOunces;
    public bool $active;

    public function __construct(int $id, int $productId, string $name, float $price, ?float $volumeOunces, bool $active)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->volumeOunces = $volumeOunces;
        $this->active = $active;
    }
}
