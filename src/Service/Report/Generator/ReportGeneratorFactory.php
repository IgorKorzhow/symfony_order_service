<?php

namespace App\Service\Report\Generator;

use App\Enum\ReportTypeEnum;
use App\Exception\UnknownEnumTypeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

readonly class ReportGeneratorFactory
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws UnknownEnumTypeException
     */
    public function make(ReportTypeEnum $typeEnum): ReportGeneratorInterface
    {
        return match ($typeEnum) {
            ReportTypeEnum::PRODUCT_CELLED_REPORT => $this->container->get(ProductCellFileReportGenerator::class),
            default => throw new UnknownEnumTypeException('Unknown enum type: '.$type.' for enum: '.self::class),
        };
    }
}
