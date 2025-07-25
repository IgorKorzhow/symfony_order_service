<?php

declare(strict_types=1);

namespace App\Service\Report\Generator;

use App\Enum\ReportTypeEnum;
use App\Exception\UnknownEnumTypeException;

readonly class ReportGeneratorFactory
{
    public function __construct(
        private ProductCellFileReportGenerator $productCellFileReportGenerator,
    ) {
    }

    /**
     * @throws UnknownEnumTypeException
     */
    public function make(ReportTypeEnum $typeEnum): ReportGeneratorInterface
    {
        return match ($typeEnum) {
            ReportTypeEnum::PRODUCT_CELLED_REPORT => $this->productCellFileReportGenerator,
            default => throw new UnknownEnumTypeException('Unknown enum type: ' . $typeEnum->value . ' for enum: ' . self::class),
        };
    }
}
