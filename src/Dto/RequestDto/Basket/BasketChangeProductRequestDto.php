<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\Basket;

use App\Entity\Product;
use App\Validator\ExistsEntityByField;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class BasketChangeProductRequestDto
{
    public function __construct(
        #[Assert\NotBlank, ExistsEntityByField(Product::class, 'id')]
        #[Groups(['json'])]
        public ?int $productId,

        #[Assert\NotBlank]
        #[Groups(['json'])]
        public int $count,
    ) {
    }
}
