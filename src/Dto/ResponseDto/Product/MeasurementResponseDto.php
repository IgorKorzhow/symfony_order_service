<?php

namespace App\Dto\ResponseDto\Product;

class MeasurementResponseDto
{
    public function __construct(
        public int $weight,
        public int $height,
        public int $width,
        public int $length,
    )
    {
    }
}
