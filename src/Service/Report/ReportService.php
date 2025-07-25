<?php

namespace App\Service\Report;

use App\Dto\RequestDto\Report\ReportOrderGenerationRequestDto;
use App\Entity\Report;
use App\Entity\ReportDetail;
use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use App\Exception\UnknownEnumTypeException;
use App\Message\Report\ReportOrderedMessage;
use App\Repository\ReportRepository;
use App\Service\Report\Generator\ReportGeneratorFactory;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ReportService
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ReportRepository $reportRepository,
        private ReportGeneratorFactory $reportGeneratorFactory,
    ) {
    }

    /**
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface
     */
    public function orderReportGeneration(ReportOrderGenerationRequestDto $requestDto): Report
    {
        $report = new Report();

        $report->setReportType(ReportTypeEnum::typeByString($requestDto->reportType));
        $report->setStatus(ReportStatusEnum::CREATED);
        $report->setDateFrom($requestDto->dateFrom);
        $report->setDateTo($requestDto->dateTo);

        $this->reportRepository->store($report);

        $this->messageBus->dispatch(new ReportOrderedMessage(
            reportId: $report->getId(),
        ));

        return $report;
    }

    public function generateReport(Report $report): Report
    {
        try {
            $reportGenerator = $this->reportGeneratorFactory->make($report->getReportType());

            $report = $reportGenerator->generate($report);
            $report->setStatus(ReportStatusEnum::SUCCESS);
        } catch (\Throwable $e) {
            $report->setStatus(ReportStatusEnum::ERROR);
            $report->setDetail(new ReportDetail(
                message: $e->getMessage(),
                error: $e->getTraceAsString(),
            ));
        }

        $this->reportRepository->flush();

        return $report;
    }
}
