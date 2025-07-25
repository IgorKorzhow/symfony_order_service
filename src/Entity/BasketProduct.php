<?php

namespace App\Entity;

class BasketProduct
{
    public function __construct(
        public int $productId,
        public int $count,
        public int $price,
    )
    {
    }
}
