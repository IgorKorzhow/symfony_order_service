<?php

namespace App\Service\Basket;

use App\Dto\BasketDto;
use App\Dto\BasketProductDto;

interface BasketServiceInterface
{
    public function getBasket(int|string $userId): BasketDto;

    public function addProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function deleteProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function updateProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;

    public function changeProduct(BasketDto $basket, BasketProductDto $basketProduct): BasketDto;
}
