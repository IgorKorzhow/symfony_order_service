<?php

namespace App\Service\Order;

use App\Dto\Order\OrderDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Exception\UnknownEnumTypeException;
use App\Repository\OrderRepository;

final readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
    )
    {
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function createOrder(OrderDto $orderDto): Order
    {
        $order = new Order();

        foreach ($orderDto->getBasket()->getProducts() as $product) {
            $orderItem = new OrderItem();

            $orderItem->setProduct($product->getProduct());
            $orderItem->setQuantity($product->getQuantity());
            $orderItem->setPrice($product->getPrice());

            $order->addOrderItem($orderItem);
        }

        $order->setTotalPrice($orderDto->getBasket()->getTotalPrice());
        $order->setDeliveryType(DeliveryTypeEnum::typeByString($orderDto->getDeliveryType()));
        $order->setOrderStatus(OrderStatusEnum::typeByString($orderDto->getOrderStatus()));

        return $this->orderRepository->store($order);
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
