<?php

namespace App\Tests\Unit\Serializer;

use App\Enum\ReportStatusEnum;
use App\Factory\Message\ReportGeneratedMessageFactory;
use App\Message\Report\ReportGeneratedMessage;
use App\Serializer\ReportGeneratedMessageSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReportGeneratedMessageSerializerTest extends TestCase
{
    private ReportGeneratedMessageFactory $factory;
    private ReportGeneratedMessageSerializer $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {

        $this->factory = new ReportGeneratedMessageFactory($this->createMock(ValidatorInterface::class));
        $this->serializer = new ReportGeneratedMessageSerializer($this->factory);
    }

    public function testEncode(): void
    {
        $uuid = Uuid::v4();

        $message = new ReportGeneratedMessage(
            id: $uuid,
            result: ReportStatusEnum::CREATED,
        );

        $envelope = new Envelope($message);

        $result = $this->serializer->encode($envelope);

        $this->assertArrayHasKey('body', $result);
        $decoded = json_decode($result['body'], true);

        $this->assertSame($uuid->toString(), $decoded['reportId']);
        $this->assertSame(ReportStatusEnum::CREATED->value, $decoded['result']);
    }

    public function testEncodeThrowsForInvalidMessage(): void
    {
        $envelope = new Envelope(new \stdClass());

        $this->expectException(InvalidArgumentException::class);
        $this->serializer->encode($envelope);
    }

    public function testDecode(): void
    {
        $uuid = Uuid::v4();

        $data = [
            'reportId' => $uuid,
            'result' => ReportStatusEnum::CREATED->value,
        ];

        $json = json_encode($data);

        $reportGeneratedMessage = new ReportGeneratedMessage(
            id: $uuid,
            result: ReportStatusEnum::CREATED
        );

        $envelope = $this->serializer->decode(['body' => $json]);

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertSame($reportGeneratedMessage->getId()->toString(), $envelope->getMessage()->getId()->toString());
        $this->assertSame($reportGeneratedMessage->getResult(), $envelope->getMessage()->getResult());
    }

    public function testDecodeThrowsForInvalidJson(): void
    {
        $this->expectException(MessageDecodingFailedException::class);

        $this->serializer->decode(['body' => 'invalid-json']);
    }
}
