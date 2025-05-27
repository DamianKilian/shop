<?php

namespace App\Payment;

use Illuminate\Support\Manager;
use App\Integrations\Interfaces\PaymentInterface;

/**
 * @mixin PaymentInterface
 */
class PaymentManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('my.payment');
    }
}
