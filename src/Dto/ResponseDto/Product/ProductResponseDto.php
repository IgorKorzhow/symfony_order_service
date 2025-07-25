<?php

namespace App\Dto\ResponseDto\Product;

use DateTimeImmutable;

class ProductResponseDto
{
    public function __construct(
        public int $id,
        public ?string $name,
        public MeasurementResponseDto $measurements,
        public ?string $description,
        public ?int $cost,
        public ?int $tax,
        public ?int $version,
        public DateTimeImmutable $createdAt,
    )
    {
    }
}
