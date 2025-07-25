<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Enum\ReportStatusEnum;
use App\Message\Report\ReportGeneratedMessage;
use App\Message\Report\ReportOrderedMessage;
use App\Repository\ReportRepository;
use App\Service\Report\ReportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
readonly class ReportOrderedHandler
{
    public function __construct(
        private ReportRepository $reportRepository,
        private ReportService $reportService,
        private LoggerInterface $logger,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ReportOrderedMessage $message): void
    {
        $this->logger->info(sprintf('Order report message received: %s', json_encode($message)));

        $report = $this->reportRepository->find($message->getReportId());

        if ($report->getStatus() !== ReportStatusEnum::CREATED) {
            return;
        }

        $report = $this->reportService->generateReport($report);

        $this->messageBus->dispatch(new ReportGeneratedMessage(
            id: $report->getId(),
            result: $report->getStatus(),
            detail: $report->getDetail(),
        ));

        $this->logger->info(sprintf('Report message handled: %s', json_encode($report)));
    }
}
