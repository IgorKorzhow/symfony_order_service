<?php

namespace App\Tests\Feature\Order;

use App\Dto\Basket\BasketDto;
use App\Dto\Basket\BasketProductDto;
use App\Entity\Product;
use App\Enum\OrderStatusEnum;
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
