<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto\Report;

use App\Entity\ReportDetail;
use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use Symfony\Component\Uid\Uuid;

class ReportResponseDto
{
    public function __construct(
        public Uuid $id,
        public ReportTypeEnum $reportType,
        public ReportStatusEnum $status,
        public ?ReportDetail $detail,
        public ?string $filePath,
        public \DateTimeImmutable $dateFrom,
        public \DateTimeImmutable $dateTo,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
