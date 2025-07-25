<?php

namespace App\Service\Basket;

use App\Entity\Basket;
use App\Entity\BasketProduct;

interface BasketServiceInterface
{
    public function getBasket(int|string $userId): Basket;
    public function deleteBasket(Basket $basket): bool;

    public function addProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function deleteProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function updateProduct(Basket $basket, BasketProduct $basketProduct): Basket;

    public function changeProduct(Basket $basket, BasketProduct $basketProduct): Basket;
}
