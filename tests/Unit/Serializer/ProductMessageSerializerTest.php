<?php

namespace App\Tests\Unit\Serializer;

use App\Factory\Message\ProductMessageFactory;
use App\Message\Product\Measurement;
use App\Message\Product\ProductMessage;
use App\Serializer\ProductMessageSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use InvalidArgumentException;

class ProductMessageSerializerTest extends TestCase
{
    private ProductMessageFactory&MockObject $factory;
    private ProductMessageSerializer $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factory = $this->createMock(ProductMessageFactory::class);
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

        $this->expectException(InvalidArgumentException::class);
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
                'width' => 10,
                'height' => 20,
                'length' => 30,
                'weight' => 40,
            ]
        ];

        $json = json_encode($data);

        $productMessage = new ProductMessage(
            id: 123,
            name: 'Test Product',
            description: 'Test Description',
            cost: 100,
            tax: 20,
            version: 1,
            measurements: new Measurement(10, 20, 30, 40),
        );

        $this->factory
            ->expects($this->once())
            ->method('fromArray')
            ->with($data)
            ->willReturn($productMessage);

        $envelope = $this->serializer->decode(['body' => $json]);

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertSame($productMessage, $envelope->getMessage());
    }

    public function testDecodeThrowsForInvalidJson(): void
    {
        $this->expectException(MessageDecodingFailedException::class);

        $this->serializer->decode(['body' => 'invalid-json']);
    }
}
