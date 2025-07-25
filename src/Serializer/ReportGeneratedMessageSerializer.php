<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Factory\Message\ReportGeneratedMessageFactory;
use App\Message\Report\ReportGeneratedMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class ReportGeneratedMessageSerializer implements SerializerInterface
{
    public function __construct(
        private ReportGeneratedMessageFactory $reportGeneratedMessageFactory,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);

        if (!$data) {
            throw new MessageDecodingFailedException('Invalid report generated message format: ' . json_encode($encodedEnvelope));
        }

        return new Envelope($this->reportGeneratedMessageFactory->fromArray($data));
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!$message instanceof ReportGeneratedMessage) {
            throw new \InvalidArgumentException('Expected ReportGeneratedMessage message');
        }

        $data = [
            'reportId' => $message->getId(),
            'result' => $message->getResult()->value,
            'detail' => $message->getDetail()
                ? [
                    'error' => $message->getDetail()->getError(),
                    'message' => $message->getDetail()->getMessage(),
                ]
                : [],
        ];

        return [
            'key' => '',
            'headers' => [],
            'body' => json_encode($data),
        ];
    }
}
