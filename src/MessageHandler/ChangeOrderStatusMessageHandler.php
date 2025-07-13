<?php

namespace App\MessageHandler;

use App\Entity\Order;
use App\Enum\OrderStatusEnum;
use App\Exception\UnknownEnumTypeException;
use App\Message\Order\ChangeOrderStatusMessage;
use App\Repository\OrderRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ChangeOrderStatusMessageHandler
{
    public function __construct(
        private OrderRepository $repository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function __invoke(ChangeOrderStatusMessage $message): void
    {
        $this->logger->info(sprintf('Change order status message received: %s', json_encode($message)));

        /** @var Order $order */
        $order = $this->repository->find($message->getOrderId());

        $order->setOrderStatus(OrderStatusEnum::typeByString($message->getStatus()));

        if ($order->getOrderStatus() === OrderStatusEnum::PAYED) {
            $order->setPayedAt($message->getPayedAt());
        }

        $order = $this->repository->store($order);

        $this->logger->info(sprintf('Change order status message completed: %s', json_encode($order)));
    }
}
