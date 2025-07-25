<?php

declare(strict_types=1);

namespace App\Message\Order;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('kafka_change_order_status_consumer')]
class ChangeOrderStatusMessage
{
    public function __construct(
        private int $orderId,
        private string $status,
        private ?\DateTimeImmutable $payedAt = null,
    ) {
    }

    public function getPayedAt(): ?\DateTimeImmutable
    {
        return $this->payedAt;
    }

    public function setPayedAt(?\DateTimeImmutable $payedAt): void
    {
        $this->payedAt = $payedAt;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
