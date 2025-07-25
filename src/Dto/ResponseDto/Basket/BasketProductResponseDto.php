<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto\Basket;

class BasketProductResponseDto
{
    public function __construct(
        public ?int $productId,
        public int $count,
        public ?int $price = 0,
    ) {
    }
}
