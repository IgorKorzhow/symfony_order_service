<?php

namespace App\Entity;

use App\Dto\AbstractValidationDto;

class BasketProduct extends AbstractValidationDto
{
    public function __construct(
        public int $productId,
        public int $count,
        public int $price,
    )
    {
    }
}
