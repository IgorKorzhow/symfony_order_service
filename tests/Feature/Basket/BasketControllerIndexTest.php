<?php

namespace App\Tests\Feature\Basket;

use App\Entity\Basket;
use App\Tests\Helpers\Helpers;
use App\Tests\Override\Interface\TestCacheResetInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Foundry\Test\Factories;

final class BasketControllerIndexTest extends WebTestCase
{
    use Helpers;
    use Factories;

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
                new Basket(
                    userId: 1,
                    products: [],
                )
            );

        $this->client->getContainer()->set(CacheInterface::class, $this->cacheMock);
    }


    #[DataProvider('basketIndexDataProvider')]
    public function testIndex(array $basket): void
    {

        $this->client->request('GET', '/api/basket', server: [
            'HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN',
        ]);

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($basket, $data);
    }

    public static function basketIndexDataProvider(): array
    {
        return [[
                'basket' => [
                    "userId" => 1,
                    "products" => [],
                    "totalPrice" => 0,
            ]]
        ];
    }

    public function testUnauthorized(): void
    {
        $this->client->catchExceptions(true);
        $this->client->request('GET', '/api/basket');

        $this->assertResponseStatusCodeSame(401);
    }
}
