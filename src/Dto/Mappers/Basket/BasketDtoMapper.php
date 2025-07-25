<?php

namespace App\Dto\Mappers\Basket;

use App\Dto\Mappers\DtoMapperInterface;
use App\Dto\ResponseDto\Basket\BasketResponseDto;
use App\Entity\Basket;

class BasketDtoMapper implements DtoMapperInterface
{
    public function __construct(
        private readonly BasketProductDtoMapper $basketProductDtoMapper,
    )
    {
    }

    public function entityToDto(object $entity): object
    {
        /** @var Basket $entity */
        return new BasketResponseDto(
            userId: $entity->userId,
            products: $this->basketProductDtoMapper->arrayEntityToDto($entity->products),
            totalPrice:  $entity->totalPrice,
        );
    }

    public function arrayEntityToDto(array $data): array
    {
        return array_map(fn(object $entity) => $this->entityToDto($entity), $data);
    }
}
