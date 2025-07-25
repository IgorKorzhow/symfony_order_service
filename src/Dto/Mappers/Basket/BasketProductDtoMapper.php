<?php

namespace App\Dto\Mappers\Basket;

use App\Dto\Mappers\DtoMapperInterface;
use App\Dto\ResponseDto\Basket\BasketProductResponseDto;
use App\Entity\BasketProduct;

class BasketProductDtoMapper implements DtoMapperInterface
{

    public function entityToDto(object $entity): object
    {
        /** @var BasketProduct $entity */
        return new BasketProductResponseDto(
            productId: $entity->productId,
            count: $entity->count,
            price: $entity->price,
        );
    }

    public function arrayEntityToDto(array $data): array
    {
        return array_map(fn(object $entity) => $this->entityToDto($entity), $data);
    }
}
