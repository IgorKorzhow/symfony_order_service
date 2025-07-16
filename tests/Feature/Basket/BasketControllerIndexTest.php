<?php

namespace App\Tests\Feature\Basket;

use App\Factory\Entity\ProductFactory;
use App\Message\Product\Measurement;
use App\Tests\Helpers\Helpers;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class BasketControllerIndexTest extends WebTestCase
{
    use Helpers;
    use Factories;

    #[DataProvider('basketIndexDataProvider')]
    public function testIndex(array $basket): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/basket', server: [
            'HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN',
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

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

//    public function testUnauthorized(): void
//    {
//        $client = self::createClient();
//
//        $client->catchExceptions(true);
//
//        $client->request('GET', '/api/basket');
//
//        $this->assertResponseStatusCodeSame(401);
//    }
}
