<?php

namespace App\Service\Order;

use App\Dto\Basket\BasketProductDto;
use App\Dto\Order\OrderDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Exception\UnknownEnumTypeException;
use App\Helpers\ArrayHelpers;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

final readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private OrderItemRepository $orderItemRepository,
    )
    {
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function createOrder(OrderDto $orderDto): Order
    {
        $order = new Order();

        $order->setUserId($orderDto->getUserId());
        $order->setTotalPrice($orderDto->getBasket()->getTotalPrice());
        $order->setDeliveryType(DeliveryTypeEnum::typeByString($orderDto->getDeliveryType()));
        $order->setOrderStatus(OrderStatusEnum::typeByString($orderDto->getOrderStatus()));

        $order = $this->orderRepository->store($order);

        $productIds = ArrayHelpers::pluck($orderDto->getBasket()->getProducts(), 'productId');
        $productsGroupedById = ArrayHelpers::groupBy($this->productRepository->findBy(['id' => $productIds]), 'id');

        foreach ($orderDto->getBasket()->getProducts() as $productDto) {
            /** @var BasketProductDto $product */
            $orderItem = new OrderItem();

            $orderItem->setProduct(ArrayHelpers::first($productsGroupedById[$productDto->getProductId()]));
            $orderItem->setQuantity($productDto->getCount());
            $orderItem->setPrice($productDto->getPrice());

            $this->orderItemRepository->persist($orderItem);

            $order->addOrderItem($orderItem);
        }

        $this->orderItemRepository->flush();

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
