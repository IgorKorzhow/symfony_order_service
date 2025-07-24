<?php
declare(strict_types=1);

namespace App\Dto\Basket;

use App\Dto\AbstractValidationDto;
use App\Exception\Basket\ProductAlreadyExistsException;
use App\Exception\Basket\ProductDoesntExistsException;
use App\Exception\Basket\ProductPriceNotFoundException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class BasketDto extends AbstractValidationDto
{
    public function __construct(int|string $userId, array $products = [])
    {
        $this->userId = $userId;
        $this->products = $products;

        $this->totalPrice = array_reduce(
            $this->products,
            fn(int $carry, BasketProductDto $product) => $carry + $product->getPrice() * $product->getCount(),
            0
        );
    }

    #[Assert\NotBlank]
    #[Groups(['json'])]
    private int|string $userId;

    #[Assert\NotNull]
    #[Assert\Valid]
    #[Groups(['json'])]
    /** @var BasketProductDto[] $products */
    private array $products;

    #[Assert\NotNull, Assert\Positive]
    #[Groups(['json'])]
    private float $totalPrice;

    public function getUserId(): int|string
    {
        return $this->userId;
    }

    public function setUserId(int|string $userId): void
    {
        $this->userId = $userId;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }


    /**
     * @throws ProductAlreadyExistsException
     * @throws ProductPriceNotFoundException
     */
    public function addProduct(BasketProductDto $basketProduct): BasketDto
    {
        $product = array_find(
            $this->products,
            fn(BasketProductDto $product) => $product->getProductId() === $basketProduct->getProductId()
        );

        if (isset($product)) {
            throw new ProductAlreadyExistsException(
                "Product with id: " . $product->getProductId() . " already exists in basket"
            );
        }

        if ($basketProduct->getPrice() === null) {
            throw new ProductPriceNotFoundException(
                "In Basket product with id: " . $basketProduct->getProductId() . " has no price"
            );
        }

        $this->products[] = $basketProduct;
        $this->totalPrice += $basketProduct->getCount() * $basketProduct->getPrice();

        return $this;
    }

    public function deleteProduct(BasketProductDto $basketProduct): BasketDto
    {
        $this->totalPrice -= $basketProduct->getCount() * $basketProduct->getPrice();

        $this->products = array_filter(
            $this->getProducts(),
            fn(BasketProductDto $savedProduct) => $savedProduct->getProductId() !== $basketProduct->getProductId()
        );

        return $this;
    }

    /**
     * @throws ProductDoesntExistsException
     * @throws ProductAlreadyExistsException
     */
    public function changeProduct(BasketProductDto $basketProduct): BasketDto
    {
        $productIdx = array_find_key(
            $this->getProducts(),
            fn(BasketProductDto $product) => $product->getProductId() === $basketProduct->getProductId()
        );

        if (!isset($productIdx) && $basketProduct->getCount() > 0) {
            return $this->addProduct($basketProduct);
        }

        if ($basketProduct->getCount() <= 0) {
            return $this->deleteProduct($basketProduct);
        }

        return $this->updateProduct($basketProduct);
    }


    /**
     * @throws ProductDoesntExistsException
     * @throws ProductPriceNotFoundException
     */
    public function updateProduct(BasketProductDto $basketProduct): BasketDto
    {
        $productIdx = array_find_key(
            $this->products,
            fn(BasketProductDto $product) => $product->getProductId() === $basketProduct->getProductId()
        );

        if (!isset($productIdx)) {
            throw new ProductDoesntExistsException(
                "Product with id: " . $basketProduct->getProductId() . " does not exist in basket"
            );
        }

        $this->totalPrice -= $this->products[$productIdx]->getCount() * $this->products[$productIdx]->getPrice();

        if ($basketProduct->getPrice() === null) {
            throw new ProductPriceNotFoundException(
                "In Basket product with id: " . $basketProduct->getProductId() . " has no price"
            );
        }

        $this->products[$productIdx] = $basketProduct;
        $this->totalPrice += $basketProduct->getCount() * $basketProduct->getPrice();

        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
}
