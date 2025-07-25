<?php

declare(strict_types=1);

namespace App\Service\Paginator;

use Symfony\Component\Serializer\Attribute\Groups;

class PaginatedListEntity
{
    #[Groups(['json'])]
    private int $page;

    #[Groups(['json'])]
    private int $perPage;

    #[Groups(['json'])]
    private int $total;

    #[Groups(['json'])]
    private array $data;

    public function __construct(int $page, int $perPage, int $total, array $data)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->total = $total;
        $this->data = $data;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTotalPages(): int
    {
        return ceil($this->total / $this->perPage);
    }
}
