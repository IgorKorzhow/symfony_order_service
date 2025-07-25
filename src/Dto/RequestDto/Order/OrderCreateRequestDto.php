<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\Order;

use App\Enum\DeliveryTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class OrderCreateRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [DeliveryTypeEnum::class, 'values'])]
        public ?string $deliveryType,

        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^\+375(25|29|33|44)\d{7}$/',
            message: 'Please enter a valid Belarusian phone number, e.g. +375291234567'
        )]
        public ?string $phone,
    ) {
    }
}
