<?php

declare(strict_types=1);

namespace App\Dto\RequestDto\Report;

use App\Enum\ReportTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ReportOrderGenerationRequestDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('string')]
        #[Assert\Choice(callback: [ReportTypeEnum::class, 'values'])]
        public string $reportType,

        #[Assert\NotNull]
        #[Assert\Type('\DateTimeImmutable')]
        public \DateTimeImmutable $dateFrom,

        #[Assert\NotNull]
        #[Assert\Type('\DateTimeImmutable')]
        #[Assert\GreaterThanOrEqual(propertyPath: 'dateFrom')]
        public \DateTimeImmutable $dateTo,
    ) {
    }
}
