<?php

declare(strict_types=1);

namespace App\Entity;

class BasketProduct
{
    public function __construct(
        public int $productId,
        public int $count,
        public int $price,
    ) {
    }
}
