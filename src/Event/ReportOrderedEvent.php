<?php

namespace App\Event;

use App\Entity\Report;

class ReportOrderedEvent
{
    public function __construct(
        private Report $report,
        private \DateTimeImmutable $occurredOn,
    )
    {
    }

    public function getReport(): Report
    {
        return $this->report;
    }

    public function setReport(Report $report): void
    {
        $this->report = $report;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function setOccurredOn(\DateTimeImmutable $occurredOn): void
    {
        $this->occurredOn = $occurredOn;
    }
}
