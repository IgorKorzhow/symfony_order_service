<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\RequestDto\Order\OrderChangeStatusRequestDto;
use App\Dto\RequestDto\Order\OrderCreateRequestDto;
use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Order;
use App\Entity\Product;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Exception\Order\OrderHasntProductsException;
use App\Exception\Order\TooManyProductsInOrderException;
use App\Exception\UnknownEnumTypeException;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Order\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private OrderService $orderService;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->orderService = new OrderService(
            $this->orderRepository,
            $this->productRepository,
            $this->entityManager
        );
    }

    public function testCreateOrderSuccess(): void
    {
        $requestDto = new OrderCreateRequestDto(
            deliveryType: DeliveryTypeEnum::COURIER->value,
            phone: '+375291234567'
        );

        $basket = new Basket(
            userId: 123,
            products: [
                new BasketProduct(productId: 1, count: 1, price: 100),
                new BasketProduct(productId: 2, count: 1, price: 200),
            ]
        );

        $product1 = new Product();
        $product1->setCost(100);
        $product1->setId(1);

        $product2 = new Product();
        $product2->setCost(200);
        $product2->setId(2);

        $this->productRepository
            ->method('findBy')
            ->with(['id' => [1, 2]])
            ->willReturn([$product1, $product2]);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($order) {
                return $order instanceof Order
                    && count($order->getOrderItems()) === 2;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $order = $this->orderService->createOrder($requestDto, $basket);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(123, $order->getUserId());
        $this->assertEquals(300, $order->getTotalPrice());
        $this->assertEquals(DeliveryTypeEnum::COURIER, $order->getDeliveryType());
        $this->assertEquals(OrderStatusEnum::CREATED, $order->getOrderStatus());
        $this->assertCount(2, $order->getOrderItems());
    }

    public function testCreateOrderWithEmptyBasket(): void
    {
        $requestDto = new OrderCreateRequestDto(
            deliveryType: DeliveryTypeEnum::COURIER->value,
            phone: '+375291234567'
        );

        $basket = new Basket(userId: 123, products: []);

        $this->expectException(OrderHasntProductsException::class);
        $this->expectExceptionMessage('Basket must be at least 1 product');

        $this->orderService->createOrder($requestDto, $basket);
    }

    public function testCreateOrderWithTooManyProducts(): void
    {
        $requestDto = new OrderCreateRequestDto(
            deliveryType: DeliveryTypeEnum::COURIER->value,
            phone: '+375291234567'
        );

        $products = [];
        for ($i = 1; $i <= 21; ++$i) {
            $products[] = new BasketProduct(productId: $i, count: 1, price: 100);
        }

        $basket = new Basket(userId: 123, products: $products);

        $this->expectException(TooManyProductsInOrderException::class);
        $this->expectExceptionMessage('Basket must be at most 20 products');

        $this->orderService->createOrder($requestDto, $basket);
    }

    public function testCreateOrderWithInvalidDeliveryType(): void
    {
        $requestDto = new OrderCreateRequestDto(
            deliveryType: 'invalid_type',
            phone: '+375291234567'
        );

        $basket = new Basket(
            userId: 123,
            products: [new BasketProduct(productId: 1, count: 1, price: 100)]
        );

        $this->productRepository
            ->method('findBy')
            ->willReturn([new Product()]);

        $this->expectException(UnknownEnumTypeException::class);

        $this->orderService->createOrder($requestDto, $basket);
    }

    public function testChangeOrderStatusSuccess(): void
    {
        $order = new Order();
        $order->setOrderStatus(OrderStatusEnum::CREATED);

        $statusRequestDto = new OrderChangeStatusRequestDto(
            orderStatus: OrderStatusEnum::PAYED->value
        );

        $this->orderRepository
            ->expects($this->once())
            ->method('store')
            ->with($order)
            ->willReturn($order);

        $result = $this->orderService->changeOrderStatus($order, $statusRequestDto->orderStatus);

        $this->assertSame($order, $result);
        $this->assertEquals(OrderStatusEnum::PAYED, $result->getOrderStatus());
    }

    public function testChangeOrderStatusWithInvalidStatus(): void
    {
        $order = new Order();
        $statusRequestDto = new OrderChangeStatusRequestDto(
            orderStatus: 'invalid_status'
        );

        $this->expectException(UnknownEnumTypeException::class);

        $this->orderService->changeOrderStatus($order, $statusRequestDto->orderStatus);
    }
}
