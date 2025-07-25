<?php

namespace App\Entity;

use App\Enum\ReportStatusEnum;
use App\Enum\ReportTypeEnum;
use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PharIo\Manifest\Type;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('json')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('json')]
    private ReportTypeEnum $reportType;

    #[ORM\Column(length: 50)]
    #[Groups('json')]
    private ReportStatusEnum $status;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups('json')]
    private ?ReportDetail $detail = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('json')]
    private ?string $filePath = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups('json')]
    private \DateTimeImmutable $dateFrom;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups('json')]
    private \DateTimeImmutable $dateTo;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups('json')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getReportType(): ReportTypeEnum
    {
        return $this->reportType;
    }

    public function setReportType(ReportTypeEnum $reportType): static
    {
        $this->reportType = $reportType;

        return $this;
    }

    public function getStatus(): ReportStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ReportStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateFrom(): \DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeImmutable $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): \DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeImmutable $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDetail(): ?ReportDetail
    {
        return $this->detail;
    }

    public function setDetail(ReportDetail $detail): void
    {
        $this->detail = $detail;
    }
}
