<?php

namespace App\Service\Basket;

use App\DTO\Basket;
use App\DTO\BasketProduct;

interface BasketServiceInterface
{
    public function getBasket(int|string $userId);

    public function addProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function deleteProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function updateProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function changeProduct(Basket $basket, BasketProduct $basketProduct): Basket;
}
