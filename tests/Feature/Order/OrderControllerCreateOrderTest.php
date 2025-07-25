<?php

declare(strict_types=1);

namespace App\Tests\Feature\Order;

use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Product;
use App\Exception\Basket\ProductAlreadyExistsException;
use App\Exception\Basket\ProductPriceNotFoundException;
use App\Factory\Entity\ProductFactory;
use App\Tests\Helpers\Helpers;
use App\Tests\Override\Interface\TestCacheResetInterface;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Foundry\Test\Factories;

final class OrderControllerCreateOrderTest extends WebTestCase
{
    use Helpers;
    use Factories;

    private $cacheMock;
    private $client;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->cacheMock = $this->createMock(TestCacheResetInterface::class);

        $products = ProductFactory::new()->many(5)->create();

        $this->cacheMock->method('get')
            ->willReturn($this->createBasketDto(1, $products));

        $this->cacheMock->method('delete')
            ->willReturn(true);

        $this->cacheMock->method('reset')->willReturn(null);

        $this->client->getContainer()->set(CacheInterface::class, $this->cacheMock);
    }

    /**
     * @throws ProductAlreadyExistsException
     * @throws ProductPriceNotFoundException
     */
    private function createBasketDto(int $userId, array $products, int $productsCount = 3): Basket
    {
        $dto = new Basket(userId: $userId);

        foreach ($products as $product) {
            /** @var $product Product */
            $basketProductDto = new BasketProduct(
                productId: $product->getId(),
                count: $productsCount,
                price: 0
            );

            $dto->addProduct($basketProductDto);
        }

        return $dto;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    #[NoReturn]
    public function testCreateOrder(): void
    {
        $this->client->request('POST', '/api/order',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN', 'CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'deliveryType' => 'courier',
                'phone' => '+375298221812',
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(201);

        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('totalPrice', $data);
        $this->assertArrayHasKey('orderStatus', $data);
        $this->assertArrayHasKey('deliveryType', $data);
        $this->assertArrayHasKey('userId', $data);
    }

    public function testUnauthorized(): void
    {
        $this->client->request('POST', '/api/order');

        $this->assertResponseStatusCodeSame(401);
    }
}
