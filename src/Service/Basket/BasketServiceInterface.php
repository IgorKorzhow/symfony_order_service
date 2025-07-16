<?php

namespace App\Service\Basket;

use App\Dto\Basket\BasketDto;
use App\Dto\Basket\BasketProductDto;

interface BasketServiceInterface
{
    public function getBasket(int|string $userId): BasketDto;
    public function deleteBasket(BasketDto $basket): bool;

    public function addProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function deleteProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function updateProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function changeProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;
}
