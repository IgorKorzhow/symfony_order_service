<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Dto\RequestDto\Order\OrderCreateRequestDto;
use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Order;
use App\Entity\Product;
use App\Enum\DeliveryTypeEnum;
use App\Factory\Entity\ProductFactory;
use App\Service\Order\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderServiceIntegrationTest extends KernelTestCase
{
    private OrderService $orderService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->orderService = self::getContainer()->get(OrderService::class);
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testCreateOrderSavesToDatabase(): void
    {
        // 1. Подготовка данных (реальные продукты в БД)
        $products = ProductFactory::new()->many(2)->create();

        $this->entityManager->flush();

        // 2. Создаем корзину с продуктами
        $basket = new Basket(
            userId: 123,
            products: [
                new BasketProduct(productId: $products[0]->getId(), count: 1, price: 100),
                new BasketProduct(productId: $products[1]->getId(), count: 2, price: 200),
            ]
        );

        // 3. Вызываем сервис
        $requestDto = new OrderCreateRequestDto(
            deliveryType: DeliveryTypeEnum::COURIER->value,
            phone: '+375291234567'
        );

        $order = $this->orderService->createOrder($requestDto, $basket);

        // 4. Проверяем, что заказ сохранился в БД
        $savedOrder = $this->entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertNotNull($savedOrder);
        $this->assertCount(2, $savedOrder->getOrderItems());
        $this->assertEquals(500, $savedOrder->getTotalPrice()); // 100*1 + 200*2
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->clear();
    }
}
