<?php

namespace App\Tests\Feature\Basket;

use App\Dto\Basket\BasketDto;
use App\Dto\Basket\BasketProductDto;
use App\Factory\Entity\ProductFactory;
use App\Tests\Helpers\Helpers;
use App\Tests\Override\Interface\TestCacheResetInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Foundry\Test\Factories;

final class BasketControllerChangeProductTest extends WebTestCase
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

        $this->cacheMock->method('get')
            ->willReturnOnConsecutiveCalls(
                new BasketDto(
                    userId: 1,
                    products: [],
                ),
                new BasketDto(
                    userId: 1,
                    products: [
                        new BasketProductDto(
                            productId: 1,
                            count: 10
                        ),
                    ],
                )
            );

        $this->cacheMock->method('delete')
            ->willReturn(true);

        $this->cacheMock->method('reset')->willReturn(null);

        $this->client->getContainer()->set(CacheInterface::class, $this->cacheMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    #[DataProvider('basketIndexDataProvider')]
    public function testChangeProduct(array $basket): void
    {
        $product = ProductFactory::new()->create();

        $this->client->request('PATCH', '/api/basket/products',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN', 'CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'productId' => $product->getId(),
                'count' => 2,
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertEqualsWithExcludedFields($data, $basket, ['products.*.price']);
    }

    public static function basketIndexDataProvider(): array
    {
        return [[
                'basket' => [
                    "userId" => 1,
                    "products" => [
                        [
                            "count" => 10,
                            "productId" => 1,
                        ],
                    ],
                    "totalPrice" => 0,
            ]]
        ];
    }

    public function testUnauthorized(): void
    {
        $this->client->request('PATCH', '/api/basket/products');

        $this->assertResponseStatusCodeSame(401);
    }
}
