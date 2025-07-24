<?php

namespace App\Tests\Feature\Order;

use App\Dto\Basket\BasketDto;
use App\Dto\Basket\BasketProductDto;
use App\Entity\Product;
use App\Exception\Basket\ProductAlreadyExistsException;
use App\Exception\Basket\ProductPriceNotFoundException;
use App\Factory\Entity\OrderFactory;
use App\Factory\Entity\ProductFactory;
use App\Tests\Helpers\Helpers;
use App\Tests\Override\Interface\TestCacheResetInterface;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Zenstruck\Foundry\Test\Factories;

final class OrderControllerChangeOrderStatusTest extends WebTestCase
{
    use Helpers;
    use Factories;

    #[NoReturn]
    public function testChangeOrderStatus(): void
    {
        $client = static::createClient();

        $order = OrderFactory::new()->create();

        $client->request('PUT', '/api/order/' . $order->getId() . '/change-status',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN', 'CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'orderStatus' => 'waiting_building',
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(200);

        $this->assertArrayHasKey('orderItems', $data);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('totalPrice', $data);
        $this->assertEquals('waiting_building', $data['orderStatus']);
        $this->assertArrayHasKey('userId', $data);
    }

    #[NoReturn]
    public function testForUserPermissionDenied(): void
    {
        $client = static::createClient();

        $order = OrderFactory::new()->create();

        $client->request('PUT', '/api/order/' . $order->getId() . '/change-status',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER', 'CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'orderStatus' => 'waiting_building',
            ])
        );

        $this->assertResponseStatusCodeSame(403);
    }


    public function testUnauthorized(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/order');

        $this->assertResponseStatusCodeSame(401);
    }
}
