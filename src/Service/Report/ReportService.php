<?php

namespace App\Service\Report;

use App\Dto\ReportDto;
use App\Entity\Report;
use App\Enum\ReportTypeEnum;
use App\Event\ReportOrderedEvent;
use App\Exception\UnknownEnumTypeException;
use App\Repository\ReportRepository;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ReportService
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly ReportRepository $reportRepository,
    )
    {
    }

    /**
     * @throws UnknownEnumTypeException
     * @throws ExceptionInterface
     */
    public function orderReportGeneration(ReportDto $reportDto): Report
    {
        $report = new Report();

        $report->setReportType(ReportTypeEnum::typeByString($reportDto->getReportType()));
        $report->setDateFrom($reportDto->getDateFrom());
        $report->setDateTo($reportDto->getDateTo());

        $this->reportRepository->store($report);

        $this->messageBus->dispatch(new ReportOrderedEvent(
            report: $report,
            occurredOn: new \DateTimeImmutable()
        ));

        return $report;
    }
}
