<?php

namespace App\Dto\Product;

use App\Dto\AbstractValidationDto;

class ProductQueryDto extends AbstractValidationDto
{
    public function __construct(
        private int $page = 1,
        private int $perPage = 10,
    )
    {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }
}
