<?php

declare(strict_types=1);

namespace App\Message\Report;

use App\Entity\ReportDetail;
use App\Enum\ReportStatusEnum;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage('kafka_report_generated_producer')]
class ReportGeneratedMessage
{
    public function __construct(
        private Uuid $id,
        private ReportStatusEnum $result,
        private ?ReportDetail $detail = null,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getResult(): ReportStatusEnum
    {
        return $this->result;
    }

    public function setResult(ReportStatusEnum $result): void
    {
        $this->result = $result;
    }

    public function getDetail(): ?ReportDetail
    {
        return $this->detail;
    }

    public function setDetail(?ReportDetail $detail = null): void
    {
        $this->detail = $detail;
    }
}
