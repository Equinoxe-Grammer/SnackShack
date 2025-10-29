<?php
namespace App\Services;

use App\Repositories\PaymentMethodRepository;

class PaymentMethodService
{
    private PaymentMethodRepository $paymentMethods;

    public function __construct(?PaymentMethodRepository $paymentMethods = null)
    {
        $this->paymentMethods = $paymentMethods ?? new PaymentMethodRepository();
    }

    public function list(): array
    {
        return $this->paymentMethods->findAll();
    }
}
