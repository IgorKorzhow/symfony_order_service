<?php

namespace App\Message\Report;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage('kafka_generate_report_producer')]
class ReportOrderedMessage
{
    public function __construct(
        private Uuid $reportId,
    ) {
    }

    public function getReportId(): Uuid
    {
        return $this->reportId;
    }

    public function setReportId(Uuid $reportId): void
    {
        $this->reportId = $reportId;
    }
}
