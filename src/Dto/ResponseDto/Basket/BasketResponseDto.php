<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto\Basket;

class BasketResponseDto
{
    public function __construct(
        public int|string $userId,
        /** @var array<int, BasketProductResponseDto> $products */
        public array $products,
        public float $totalPrice,
    ) {
    }
}
