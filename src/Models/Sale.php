<?php
namespace App\Models;

class Sale
{
    public int $id;
    public string $code;
    public string $date;
    public string $time;
    public string $user;
    public string $paymentMethod;
    public float $total;

    /** @var SaleItem[] */
    public array $items = [];

    public function __construct(int $id, string $code, string $date, string $time, string $user, string $paymentMethod, float $total)
    {
        $this->id = $id;
        $this->code = $code;
        $this->date = $date;
        $this->time = $time;
        $this->user = $user;
        $this->paymentMethod = $paymentMethod;
        $this->total = $total;
    }

    public function addItem(SaleItem $item): void
    {
        $this->items[] = $item;
    }
}
