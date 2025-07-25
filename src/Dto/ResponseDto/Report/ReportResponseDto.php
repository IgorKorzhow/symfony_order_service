<?php

namespace App\Dto\ResponseDto\Report;

use App\Dto\ResponseDto\Product\MeasurementResponseDto;
use App\Entity\OrderItem;
use App\Entity\ReportDetail;
use App\Enum\DeliveryTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
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
    )
    {
    }
}
