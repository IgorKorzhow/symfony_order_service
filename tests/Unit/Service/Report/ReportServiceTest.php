<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Report;

use App\Dto\RequestDto\Report\ReportOrderGenerationRequestDto;
use App\Entity\Report;
use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use App\Exception\UnknownEnumTypeException;
use App\Message\Report\ReportOrderedMessage;
use App\Repository\ReportRepository;
use App\Service\Report\Generator\ReportGeneratorFactory;
use App\Service\Report\Generator\ReportGeneratorInterface;
use App\Service\Report\ReportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class ReportServiceTest extends TestCase
{
    private ReportService $reportService;
    private MessageBusInterface $messageBus;
    private ReportRepository $reportRepository;
    private ReportGeneratorFactory $reportGeneratorFactory;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->reportRepository = $this->createMock(ReportRepository::class);
        $this->reportGeneratorFactory = $this->createMock(ReportGeneratorFactory::class);

        $this->reportService = new ReportService(
            $this->messageBus,
            $this->reportRepository,
            $this->reportGeneratorFactory
        );
    }

    public function testOrderReportGenerationSuccess(): void
    {
        $requestDto = new ReportOrderGenerationRequestDto(
            reportType: 'product_celled_report',
            dateFrom: new \DateTimeImmutable('2023-01-01'),
            dateTo: new \DateTimeImmutable('2023-01-31')
        );

        // Мокаем сохранение отчета с установкой ID
        $this->reportRepository
            ->method('store')
            ->willReturnCallback(function (Report $report) {
                $reflection = new \ReflectionClass($report);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($report, Uuid::v4());

                return $report;
            });

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof ReportOrderedMessage
                    && $message->getReportId() instanceof Uuid;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $report = $this->reportService->orderReportGeneration($requestDto);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals(ReportTypeEnum::PRODUCT_CELLED_REPORT, $report->getReportType());
        $this->assertEquals(ReportStatusEnum::CREATED, $report->getStatus());
        $this->assertEquals($requestDto->dateFrom, $report->getDateFrom());
        $this->assertEquals($requestDto->dateTo, $report->getDateTo());
        $this->assertNotNull($report->getId());
    }

    public function testOrderReportGenerationWithInvalidType(): void
    {
        $requestDto = new ReportOrderGenerationRequestDto(
            reportType: 'invalid_type',
            dateFrom: new \DateTimeImmutable('2023-01-01'),
            dateTo: new \DateTimeImmutable('2023-01-31')
        );

        $this->expectException(UnknownEnumTypeException::class);
        $this->reportService->orderReportGeneration($requestDto);
    }

    public function testOrderReportGenerationWithMessageBusException(): void
    {
        $requestDto = new ReportOrderGenerationRequestDto(
            reportType: 'product_celled_report',
            dateFrom: new \DateTimeImmutable('2023-01-01'),
            dateTo: new \DateTimeImmutable('2023-01-31')
        );

        // Мокаем сохранение отчета с установкой ID
        $this->reportRepository
            ->method('store')
            ->willReturnCallback(function (Report $report) {
                $reflection = new \ReflectionClass($report);
                $property = $reflection->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($report, Uuid::v4());

                return $report;
            });

        // Создаем реальную исключительную ситуацию для MessageBus
        $exception = new class extends \RuntimeException implements ExceptionInterface {};

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->expectException(ExceptionInterface::class);
        $this->reportService->orderReportGeneration($requestDto);
    }

    public function testGenerateReportSuccess(): void
    {
        $report = new Report();
        $report->setReportType(ReportTypeEnum::PRODUCT_CELLED_REPORT);
        $report->setStatus(ReportStatusEnum::CREATED);

        $mockGenerator = $this->createMock(ReportGeneratorInterface::class);
        $mockGenerator->expects($this->once())
            ->method('generate')
            ->with($report)
            ->willReturn($report);

        $this->reportGeneratorFactory
            ->expects($this->once())
            ->method('make')
            ->with(ReportTypeEnum::PRODUCT_CELLED_REPORT)
            ->willReturn($mockGenerator);

        $this->reportRepository
            ->expects($this->once())
            ->method('flush');

        $result = $this->reportService->generateReport($report);

        $this->assertSame($report, $result);
        $this->assertEquals(ReportStatusEnum::SUCCESS, $result->getStatus());
    }

    public function testGenerateReportWithError(): void
    {
        $report = new Report();
        $report->setReportType(ReportTypeEnum::PRODUCT_CELLED_REPORT);
        $report->setStatus(ReportStatusEnum::CREATED);

        $exception = new \RuntimeException('Test error');

        $mockGenerator = $this->createMock(ReportGeneratorInterface::class);
        $mockGenerator->expects($this->once())
            ->method('generate')
            ->with($report)
            ->willThrowException($exception);

        $this->reportGeneratorFactory
            ->method('make')
            ->willReturn($mockGenerator);

        $this->reportRepository
            ->expects($this->once())
            ->method('flush');

        $result = $this->reportService->generateReport($report);

        $this->assertSame($report, $result);
        $this->assertEquals(ReportStatusEnum::ERROR, $result->getStatus());
        $this->assertNotNull($result->getDetail());
        $this->assertEquals('Test error', $result->getDetail()->getMessage());
    }
}
