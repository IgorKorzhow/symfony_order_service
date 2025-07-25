<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Dto\RequestDto\Order\OrderCreateRequestDto;
use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Exception\Order\OrderHasntProductsException;
use App\Exception\Order\TooManyProductsInOrderException;
use App\Exception\UnknownEnumTypeException;
use App\Helpers\ArrayHelpers;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function createOrder(OrderCreateRequestDto $requestDto, Basket $basket): Order
    {
        if (count($basket->products) < 1) {
            throw new OrderHasntProductsException('Basket must be at least 1 product');
        }

        $totalProductsCount = array_reduce($basket->products, function ($carry, BasketProduct $product) {
            return $carry + $product->count;
        }, 0);

        if ($totalProductsCount > 20) {
            throw new TooManyProductsInOrderException('Basket must be at most 20 products');
        }

        $order = new Order();

        $order->setUserId($basket->userId);
        $order->setTotalPrice($basket->totalPrice);
        $order->setDeliveryType(DeliveryTypeEnum::typeByString($requestDto->deliveryType));
        $order->setOrderStatus(OrderStatusEnum::CREATED);

        $productIds = ArrayHelpers::pluck($basket->products, 'productId');
        $productsGroupedById = ArrayHelpers::groupBy($this->productRepository->findBy(['id' => $productIds]), 'id');

        foreach ($basket->products as $product) {
            /** @var BasketProduct $product */
            $orderItem = new OrderItem();

            $orderItem->setProduct(ArrayHelpers::first($productsGroupedById[$product->productId]));
            $orderItem->setQuantity($product->count);
            $orderItem->setPrice($product->price);

            $order->addOrderItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function changeOrderStatus(Order $order, string $orderStatus): Order
    {
        $order->setOrderStatus(OrderStatusEnum::typeByString($orderStatus));

        return $this->orderRepository->store($order);
    }
}
