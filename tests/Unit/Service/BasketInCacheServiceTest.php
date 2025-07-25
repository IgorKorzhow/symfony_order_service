<?php

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
                        && $basket->getUserId() === $userId
                        && empty($basket->getProducts());
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
        $product = new BasketProduct(1, 2);
        $product->setPrice(10.0);

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
                    return count($basket->getProducts()) === 1
                        && $basket->getProducts()[0]->getProductId() === $product->getProductId()
                        && $basket->getTotalPrice() === 20.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->addProduct($initialBasket, $product);

        $this->assertCount(1, $result->getProducts());
        $this->assertEquals(20.0, $result->getTotalPrice());
    }
    public function testUpdateProduct(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $initialProduct = new BasketProduct(1, 2);
        $initialProduct->setPrice(10.0);
        $initialBasket = (new Basket($userId))->addProduct($initialProduct);

        $updatedProduct = new BasketProduct(1, 3);
        $updatedProduct->setPrice(15.0);

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
                    $product = $basket->getProducts()[0];
                    return $product->getCount() === $updatedProduct->getCount()
                        && $basket->getTotalPrice() === 45.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->updateProduct($initialBasket, $updatedProduct);

        $this->assertEquals(3, $result->getProducts()[0]->getCount());
        $this->assertEquals(45.0, $result->getTotalPrice());
    }

    public function testDeleteProduct(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $product = new BasketProduct(1, 2);
        $product->setPrice(10.0);
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
                    return count($basket->getProducts()) === 0
                        && $basket->getTotalPrice() === 0.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->deleteProduct($initialBasket, $product);

        $this->assertCount(0, $result->getProducts());
        $this->assertEquals(0.0, $result->getTotalPrice());
    }

    public function testChangeProductAddNewWhenCountPositive(): void
    {
        $userId = 123;
        $basketKey = 'basket_123';
        $initialBasket = new Basket($userId);
        $product = new BasketProduct(1, 2);
        $product->setPrice(10.0);

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
                    return count($basket->getProducts()) === 1
                        && $basket->getTotalPrice() === 20.0;
                }),
                86400
            )
            ->willReturnCallback(function ($key, $callback) {
                return $callback();
            });

        $result = $this->basketService->changeProduct($initialBasket, $product);

        $this->assertCount(1, $result->getProducts());
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
