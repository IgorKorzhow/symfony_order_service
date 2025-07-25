<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Service\Basket\BasketInCacheService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class BasketInCacheServiceTest extends TestCase
{
    private CacheInterface $cache;
    private BasketInCacheService $basketService;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->basketService = new BasketInCacheService($this->cache);
    }

    public function testGetBasket(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $expectedBasket = new Basket($userId);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $basketKey,
                $this->callback(function ($callback) use ($userId) {
                    $basket = $callback();

                    return $basket instanceof Basket
                        && $basket->userId === $userId
                        && empty($basket->products);
                }),
                86400
            )
            ->willReturn($expectedBasket);

        $result = $this->basketService->getBasket($userId);

        $this->assertEquals($expectedBasket, $result);
    }

    public function testAddProduct(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $initialBasket = new Basket($userId);
        $product = new BasketProduct(1, 2, 10);

        $this->cache->expects($this->once())
            ->method('delete')
            ->with($basketKey)
            ->willReturn(true);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $basketKey,
                $this->callback(function ($callback) use ($product) {
                    $basket = $callback();

                    return count($basket->products) === 1
                        && $basket->products[0]->productId === $product->productId
                        && $basket->totalPrice === 20.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->addProduct($initialBasket, $product);

        $this->assertCount(1, $result->products);
        $this->assertEquals(20.0, $result->totalPrice);
    }

    public function testUpdateProduct(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $initialProduct = new BasketProduct(1, 2, 10);
        $initialBasket = (new Basket($userId))->addProduct($initialProduct);

        $updatedProduct = new BasketProduct(1, 3, 15);

        $this->cache->expects($this->once())
            ->method('delete')
            ->with($basketKey)
            ->willReturn(true);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $basketKey,
                $this->callback(function ($callback) use ($updatedProduct) {
                    $basket = $callback();
                    $product = $basket->products[0];

                    return $product->count === $updatedProduct->count
                        && $basket->totalPrice === 45.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->updateProduct($initialBasket, $updatedProduct);

        $this->assertEquals(3, $result->products[0]->count);
        $this->assertEquals(45.0, $result->totalPrice);
    }

    public function testDeleteProduct(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $product = new BasketProduct(1, 2, 10);
        $initialBasket = (new Basket($userId))->addProduct($product);

        $this->cache->expects($this->once())
            ->method('delete')
            ->with($basketKey)
            ->willReturn(true);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $basketKey,
                $this->callback(function ($callback) {
                    $basket = $callback();

                    return count($basket->products) === 0
                        && $basket->totalPrice === 0.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->deleteProduct($initialBasket, $product);

        $this->assertCount(0, $result->products);
        $this->assertEquals(0.0, $result->totalPrice);
    }

    public function testChangeProductAddNewWhenCountPositive(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $initialBasket = new Basket($userId);
        $product = new BasketProduct(1, 2, 10);

        $this->cache->expects($this->once())
            ->method('delete')
            ->with($basketKey)
            ->willReturn(true);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $basketKey,
                $this->callback(function ($callback) {
                    $basket = $callback();

                    return count($basket->products) === 1
                        && $basket->totalPrice === 20.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->changeProduct($initialBasket, $product);

        $this->assertCount(1, $result->products);
    }

    public function testDeleteBasket(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $basket = new Basket($userId);

        $this->cache->expects($this->once())
            ->method('delete')
            ->with($basketKey)
            ->willReturn(true);

        $result = $this->basketService->deleteBasket($basket);

        $this->assertTrue($result);
    }
}
