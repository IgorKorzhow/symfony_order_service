<?php

namespace App\Dto\Basket;

use App\Dto\AbstractValidationDto;
use App\Entity\Product;
use App\Validator\ExistsEntityByField;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


class BasketProductDto extends AbstractValidationDto
{
    public function __construct(?int $productId = null, int $count = 0)
    {
        $this->productId = $productId;
        $this->count = $count;
    }

    #[Assert\NotBlank, ExistsEntityByField(Product::class, 'id')]
    #[Groups(['json'])]
    private ?int $productId;

    #[Assert\NotBlank]
    #[Groups(['json'])]
    private int $count;

    #[Assert\NotBlank]
    #[Groups(['json'])]
    private ?int $price = 0;

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }
}
