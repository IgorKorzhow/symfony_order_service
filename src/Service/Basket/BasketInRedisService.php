<?php
declare(strict_types=1);

namespace App\Service\Basket;

use App\DTO\Basket;
use App\DTO\BasketProduct;
use App\Exception\Basket\ProductDoesntExistsException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

final class BasketInRedisService implements BasketServiceInterface
{
    private const BASKET_PREFIX = 'basket_';

    private const CACHE_TTL = 86400;

    public function __construct(private readonly CacheInterface $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getBasket(int|string $userId)
    {
        $basketKey = $this->getBasketKey($userId);

        return $this->cache->get($basketKey, fn() => new Basket(userId: $userId), self::CACHE_TTL);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function addProduct(Basket $basket, BasketProduct $basketProduct): Basket
    {
        $userId = $basket->getUserId();

        $basketKey = $this->getBasketKey($userId);

        $basket = $basket->addProduct($basketProduct);

        $res = $this->cache->delete($basketKey);

        if (!$res) {
            throw new Exception('Cannot delete basket form cache. Basket_key:' . $basketKey);
        }

        return $this->cache->get($basketKey, fn() => $basket, self::CACHE_TTL);
    }

    /**
     * @throws ProductDoesntExistsException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function updateProduct(Basket $basket, BasketProduct $basketProduct): Basket
    {
        $userId = $basket->getUserId();

        $basketKey = $this->getBasketKey($userId);

        $basket = $basket->updateProduct($basketProduct);

        $res = $this->cache->delete($basketKey);

        if (!$res) {
            throw new Exception('Cannot delete basket form cache. Basket_key:' . $basketKey);
        }

        return $this->cache->get($basketKey, fn() => $basket, self::CACHE_TTL);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function deleteProduct(Basket $basket, BasketProduct $basketProduct): Basket
    {
        $userId = $basket->getUserId();

        $basketKey = $this->getBasketKey($userId);

        $basket = $basket->deleteProduct($basketProduct);

        $res = $this->cache->delete($basketKey);

        if (!$res) {
            throw new Exception('Cannot delete basket form cache. Basket_key:' . $basketKey);
        }

        return $this->cache->get($basketKey, fn() => $basket, self::CACHE_TTL);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function changeProduct(Basket $basket, BasketProduct $basketProduct): Basket
    {
        $userId = $basket->getUserId();

        $basketKey = $this->getBasketKey($userId);

        $basket = $basket->changeProduct($basketProduct);

        $res = $this->cache->delete($basketKey);

        if (!$res) {
            throw new Exception('Cannot delete basket form cache. Basket_key:' . $basketKey);
        }

        return $this->cache->get($basketKey, fn() => $basket, self::CACHE_TTL);
    }

    private function getBasketKey(int|string $userId): string
    {
        return self::BASKET_PREFIX . $userId;
    }
}
