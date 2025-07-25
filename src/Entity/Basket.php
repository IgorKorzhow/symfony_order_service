<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\Basket\ProductAlreadyExistsException;
use App\Exception\Basket\ProductDoesntExistsException;
use App\Exception\Basket\ProductPriceNotFoundException;

class Basket
{
    public function __construct(int|string $userId, array $products = [])
    {
        $this->userId = $userId;
        $this->products = $products;

        $this->totalPrice = array_reduce(
            $this->products,
            fn(int $carry, BasketProduct $product) => $carry + $product->price * $product->count,
            0
        );
    }

    public int $userId;

    /** @var BasketProduct[] $products */
    public array $products;

    public float $totalPrice;

    /**
     * @throws ProductAlreadyExistsException
     * @throws ProductPriceNotFoundException
     */
    public function addProduct(BasketProduct $basketProduct): Basket
    {
        /** @var BasketProduct $product */
        $product = array_find(
            $this->products,
            fn(BasketProduct $product) => $product->productId === $basketProduct->productId
        );

        if (isset($product)) {
            throw new ProductAlreadyExistsException(
                "Product with id: " . $product->productId . " already exists in basket"
            );
        }

        if ($basketProduct->price === null) {
            throw new ProductPriceNotFoundException(
                "In Basket product with id: " . $basketProduct->productId . " has no price"
            );
        }

        $this->products[] = $basketProduct;
        $this->totalPrice += $basketProduct->count * $basketProduct->price;

        return $this;
    }

    public function deleteProduct(BasketProduct $basketProduct): Basket
    {
        $this->totalPrice -= $basketProduct->count * $basketProduct->price;

        $this->products = array_filter(
            $this->products,
            fn(BasketProduct $savedProduct) => $savedProduct->productId !== $basketProduct->productId
        );

        return $this;
    }

    /**
     * @throws ProductDoesntExistsException
     * @throws ProductAlreadyExistsException|ProductPriceNotFoundException
     */
    public function changeProduct(BasketProduct $basketProduct): Basket
    {
        $productIdx = array_find_key(
            $this->products,
            fn(BasketProduct $product) => $product->productId === $basketProduct->productId
        );

        if (!isset($productIdx) && $basketProduct->count > 0) {
            return $this->addProduct($basketProduct);
        }

        if ($basketProduct->count <= 0) {
            return $this->deleteProduct($basketProduct);
        }

        return $this->updateProduct($basketProduct);
    }


    /**
     * @throws ProductDoesntExistsException
     * @throws ProductPriceNotFoundException
     */
    public function updateProduct(BasketProduct $basketProduct): Basket
    {
        $productIdx = array_find_key(
            $this->products,
            fn(BasketProduct $product) => $product->productId === $basketProduct->productId
        );

        if (!isset($productIdx)) {
            throw new ProductDoesntExistsException(
                "Product with id: " . $basketProduct->productId . " does not exist in basket"
            );
        }

        $this->totalPrice -= $this->products[$productIdx]->count * $this->products[$productIdx]->price;

        if ($basketProduct->price === null) {
            throw new ProductPriceNotFoundException(
                "In Basket product with id: " . $basketProduct->productId . " has no price"
            );
        }

        $this->products[$productIdx] = $basketProduct;
        $this->totalPrice += $basketProduct->price * $basketProduct->count;

        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
}
