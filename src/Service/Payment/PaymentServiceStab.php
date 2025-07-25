<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Order;

class PaymentServiceStab implements PaymentServiceInterface
{
    public function createPaymentUrlForOrder(int|string $userId, Order $order): string
    {
        return 'https://google.com';
    }
}
