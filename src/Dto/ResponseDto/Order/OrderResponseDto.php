<?php

namespace App\Dto\ResponseDto\Order;

use App\Dto\ResponseDto\Product\MeasurementResponseDto;
use App\Entity\OrderItem;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

class OrderResponseDto
{
    public function __construct(
        public int $id,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $payedAt,
        public int $totalPrice,
        public OrderStatusEnum $orderStatus,
        public DeliveryTypeEnum $deliveryType,
        public int $userId,
    )
    {
    }
}
