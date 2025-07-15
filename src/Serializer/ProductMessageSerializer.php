<?php

namespace App\Serializer;

use App\Factory\Message\ProductMessageFactory;
use App\Message\Product\ProductMessage;
use InvalidArgumentException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class ProductMessageSerializer implements SerializerInterface
{
    public function __construct(
        private ProductMessageFactory $productMessageFactory,
    )
    {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);

        if (!$data) {
            throw new MessageDecodingFailedException('Invalid product message format: ' . json_encode($encodedEnvelope));
        }

        return new Envelope($this->productMessageFactory->fromArray($data));
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!$message instanceof ProductMessage) {
            throw new InvalidArgumentException('Expected ProductMessage message');
        }

        $data = [
            'id' => $message->getId(),
            'name' => $message->getName(),
            'description' => $message->getDescription(),
            'cost' => $message->getCost(),
            'tax' => $message->getTax(),
            'version' => $message->getVersion(),
            'measurements' => [
                'width' => $message->getMeasurements()->getWidth(),
                'height' => $message->getMeasurements()->getHeight(),
                'length' => $message->getMeasurements()->getLength(),
                'weight' => $message->getMeasurements()->getWeight(),
            ]
        ];

        return [
            'key' => '',
            'headers' => [],
            'body' => json_encode($data),
        ];
    }
}
