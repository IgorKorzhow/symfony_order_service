<?php

namespace App\Tests\Feature\Order;

use App\Enum\OrderStatusEnum;
use App\Factory\Entity\OrderFactory;
use App\Tests\Helpers\Helpers;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class OrderControllerPayTest extends WebTestCase
{
    use Helpers;
    use Factories;

    #[NoReturn]
    public function testChangeOrderStatus(): void
    {
        $client = static::createClient();

        $order = OrderFactory::new()->create(['orderStatus' => OrderStatusEnum::CREATED, 'userId' => 1]);

        $client->request('PUT', '/api/order/' . $order->getId() . '/pay',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER', 'CONTENT_TYPE' => 'application/json']
        );

        $this->assertResponseStatusCodeSame(302);
    }

    public function testUnauthorized(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/order');

        $this->assertResponseStatusCodeSame(401);
    }
}
