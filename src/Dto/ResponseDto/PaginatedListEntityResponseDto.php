<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto;

class PaginatedListEntityResponseDto
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $total,
        public array $data,
        public int $totalPages,
    ) {
    }
}
