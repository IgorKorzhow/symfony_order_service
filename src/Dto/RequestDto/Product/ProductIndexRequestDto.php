<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\Product;

use Symfony\Component\Validator\Constraints as Assert;

class ProductIndexRequestDto
{
    public function __construct(
        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(1)]
        public int $page = 1,
        #[Assert\Type('integer')]
        #[Assert\GreaterThanOrEqual(1)]
        public int $perPage = 10,
    ) {
    }
}
