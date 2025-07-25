<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\Order;

use App\Enum\OrderStatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

class OrderChangeStatusRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [OrderStatusEnum::class, 'values'])]
        public ?string $orderStatus,
    ) {
    }
}
