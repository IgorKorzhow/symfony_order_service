<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto\Order;

use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;

class OrderResponseDto
{
    public function __construct(
        public int $id,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $payedAt,
        public float $totalPrice,
        public OrderStatusEnum $orderStatus,
        public DeliveryTypeEnum $deliveryType,
        public int $userId,
    ) {
    }
}
