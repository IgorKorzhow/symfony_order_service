<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer;

use App\Factory\Message\ProductMessageFactory;
use App\Message\Product\Measurement;
use App\Message\Product\ProductMessage;
use App\Serializer\ProductMessageSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductMessageSerializerTest extends TestCase
{
    private ProductMessageFactory $factory;
    private ProductMessageSerializer $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factory = new ProductMessageFactory($this->createMock(ValidatorInterface::class));
        $this->serializer = new ProductMessageSerializer($this->factory);
    }

    public function testEncode(): void
    {
        $message = new ProductMessage(
            id: 123,
            name: 'Test Product',
            description: 'Test Description',
            cost: 100,
            tax: 20,
            version: 1,
            measurements: new Measurement(10, 20, 30, 40),
        );

        $envelope = new Envelope($message);

        $result = $this->serializer->encode($envelope);

        $this->assertArrayHasKey('body', $result);
        $decoded = json_decode($result['body'], true);

        $this->assertSame(123, $decoded['id']);
        $this->assertSame('Test Product', $decoded['name']);
        $this->assertSame(20, $decoded['tax']);
        $this->assertSame(30, $decoded['measurements']['width']);
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
            'id' => 123,
            'name' => 'Test Product',
            'description' => 'Test Description',
            'cost' => 100,
            'tax' => 20,
            'version' => 1,
            'measurements' => [
                'width' => 30,
                'height' => 20,
                'length' => 30,
                'weight' => 10,
            ],
        ];

        $json = json_encode($data);

        $productMessage = new ProductMessage(
            id: 123,
            name: 'Test Product',
            description: 'Test Description',
            cost: 100,
            tax: 20,
            version: 1,
            measurements: new Measurement(10, 20, 30, 30),
        );

        $envelope = $this->serializer->decode(['body' => $json]);

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertSame($productMessage->getId(), $envelope->getMessage()->getId());
        $this->assertSame($productMessage->getName(), $envelope->getMessage()->getName());
        $this->assertSame($productMessage->getDescription(), $envelope->getMessage()->getDescription());
        $this->assertSame($productMessage->getCost(), $envelope->getMessage()->getCost());
        $this->assertSame($productMessage->getTax(), $envelope->getMessage()->getTax());
        $this->assertSame($productMessage->getVersion(), $envelope->getMessage()->getVersion());
        $this->assertSame($productMessage->getMeasurements()->getHeight(), $envelope->getMessage()->getMeasurements()->getHeight());
        $this->assertSame($productMessage->getMeasurements()->getLength(), $envelope->getMessage()->getMeasurements()->getLength());
        $this->assertSame($productMessage->getMeasurements()->getWidth(), $envelope->getMessage()->getMeasurements()->getWidth());
        $this->assertSame($productMessage->getMeasurements()->getWeight(), $envelope->getMessage()->getMeasurements()->getWeight());
    }

    public function testDecodeThrowsForInvalidJson(): void
    {
        $this->expectException(MessageDecodingFailedException::class);

        $this->serializer->decode(['body' => 'invalid-json']);
    }
}
