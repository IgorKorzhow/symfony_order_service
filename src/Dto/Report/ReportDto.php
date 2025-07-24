<?php

declare(strict_types=1);

namespace App\Dto\Report;

use App\Dto\AbstractValidationDto;
use App\Enum\ReportTypeEnum;
use App\Exception\ValidationException;
use Symfony\Component\Validator\Constraints as Assert;

class ReportDto extends AbstractValidationDto
{
    #[Assert\NotNull]
    #[Assert\Type('string')]
    #[Assert\Choice(callback: [ReportTypeEnum::class, 'values'])]
    private string $reportType;

    #[Assert\NotNull]
    #[Assert\Type('\DateTimeImmutable')]
    private \DateTimeImmutable $dateFrom;

    #[Assert\NotNull]
    #[Assert\Type('\DateTimeImmutable')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'dateFrom')]
    private \DateTimeImmutable $dateTo;

    public function __construct(string $reportType, string $dateFrom, string $dateTo)
    {
        $this->reportType = $reportType;

        try {
            $this->dateFrom = new \DateTimeImmutable($dateFrom);
            $this->dateTo = new \DateTimeImmutable($dateTo);
        } catch (\Exception $e) {
            throw new ValidationException('Invalid date format provided');
        }
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): void
    {
        $this->reportType = $reportType;
    }

    public function getDateFrom(): \DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeImmutable $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateTo(): \DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTimeImmutable $dateTo): void
    {
        $this->dateTo = $dateTo;
    }
}
