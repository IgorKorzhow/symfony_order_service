<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Factory\Message\ChangeOrderStatusMessageFactory;
use App\Message\Order\ChangeOrderStatusMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class ChangeOrderStatusMessageSerializer implements SerializerInterface
{
    public function __construct(
        private ChangeOrderStatusMessageFactory $factory,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);

        if (!$data) {
            throw new MessageDecodingFailedException('Invalid change order status message format: ' . json_encode($encodedEnvelope));
        }

        return new Envelope($this->factory->fromArray($data));
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!$message instanceof ChangeOrderStatusMessage) {
            throw new \InvalidArgumentException('Expected ChangeOrderStatusMessage message');
        }

        $data = [
            'orderId' => $message->getOrderId(),
            'status' => $message->getStatus(),
            'payedAt' => $message->getPayedAt(),
        ];

        return [
            'key' => '',
            'headers' => [],
            'body' => json_encode($data),
        ];
    }
}
