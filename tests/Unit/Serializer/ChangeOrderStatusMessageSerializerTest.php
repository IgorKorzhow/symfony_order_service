<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer;

use App\Enum\OrderStatusEnum;
use App\Factory\Message\ChangeOrderStatusMessageFactory;
use App\Message\Order\ChangeOrderStatusMessage;
use App\Serializer\ChangeOrderStatusMessageSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeOrderStatusMessageSerializerTest extends TestCase
{
    private ChangeOrderStatusMessageFactory $factory;
    private ChangeOrderStatusMessageSerializer $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factory = new ChangeOrderStatusMessageFactory($this->createMock(ValidatorInterface::class));
        $this->serializer = new ChangeOrderStatusMessageSerializer($this->factory);
    }

    public function testEncode(): void
    {
        $message = new ChangeOrderStatusMessage(
            orderId: 1,
            status: OrderStatusEnum::PAYED->value,
            payedAt: new \DateTimeImmutable(),
        );

        $envelope = new Envelope($message);

        $result = $this->serializer->encode($envelope);

        $this->assertArrayHasKey('body', $result);
        $decoded = json_decode($result['body'], true);

        $this->assertSame(1, $decoded['orderId']);
        $this->assertSame(OrderStatusEnum::PAYED->value, $decoded['status']);
    }

    public function testEncodeThrowsForInvalidMessage(): void
    {
        $envelope = new Envelope(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);
        $this->serializer->encode($envelope);
    }

    public function testDecode(): void
    {
        $data = [
            'orderId' => 1,
            'status' => OrderStatusEnum::PAYED->value,
            'payedAt' => (new \DateTimeImmutable())->format('Y-m-d'),
        ];

        $json = json_encode($data);

        $changeOrderStatusMessage = new ChangeOrderStatusMessage(
            orderId: 1,
            status: OrderStatusEnum::PAYED->value,
            payedAt: new \DateTimeImmutable(),
        );

        $envelope = $this->serializer->decode(['body' => $json]);

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertSame($changeOrderStatusMessage->getOrderId(), $envelope->getMessage()->getOrderId());
        $this->assertSame($changeOrderStatusMessage->getStatus(), $envelope->getMessage()->getStatus());
        $this->assertSame($changeOrderStatusMessage->getPayedAt()->format('Y-m-d'), $envelope->getMessage()->getPayedAt()->format('Y-m-d'));
    }

    public function testDecodeThrowsForInvalidJson(): void
    {
        $this->expectException(MessageDecodingFailedException::class);

        $this->serializer->decode(['body' => 'invalid-json']);
    }
}
