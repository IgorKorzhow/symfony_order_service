<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Order;

interface PaymentServiceInterface
{
    // When will implement this integration add webhock payed for sync or topic in kafka right now it be useless
    public function createPaymentUrlForOrder(int|string $userId, Order $order): string;
}
