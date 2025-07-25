<?php

namespace App\Dto\Mappers\Product;

use App\Dto\Mappers\DtoMapperInterface;
use App\Dto\ResponseDto\Product\MeasurementResponseDto;
use App\Dto\ResponseDto\Product\ProductResponseDto;
use App\Entity\Product;

class ProductDtoMapper implements DtoMapperInterface
{

    public function entityToDto(object $entity): object
    {
        /** @var Product $entity */
        return new ProductResponseDto(
            id: $entity->getId(),
            name: $entity->getName(),
            measurements: new MeasurementResponseDto(
                weight: $entity->getMeasurements()->getWeight(),
                height: $entity->getMeasurements()->getHeight(),
                width: $entity->getMeasurements()->getWidth(),
                length: $entity->getMeasurements()->getLength(),
            ),
            description: $entity->getDescription(),
            cost: $entity->getCost(),
            tax: $entity->getTax(),
            version: $entity->getVersion(),
            createdAt: $entity->getCreatedAt(),
        );
    }

    public function arrayEntityToDto(array $data): array
    {
        return array_map(fn(object $entity) => $this->entityToDto($entity), $data);
    }
}
