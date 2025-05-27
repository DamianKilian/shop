<?php

namespace App\Integrations;

use App\Payment\PaymentInterface;

class HotPay implements PaymentInterface
{
    public readonly string $baseUrl;
    public readonly array $config;

    public function __construct()
    {
        $this->config = config('hotpay');
    }

    public function pay($order, $regulationAccept = false) {}
}
