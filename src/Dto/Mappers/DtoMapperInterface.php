<?php

declare(strict_types=1);

namespace App\Dto\Mappers;

interface DtoMapperInterface
{
    public function entityToDto(object $entity): object;

    /** @return array<int, object> */
    public function arrayEntityToDto(array $data): array;
}
