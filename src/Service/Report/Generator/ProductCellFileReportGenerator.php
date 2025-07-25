<?php

declare(strict_types=1);

namespace App\Service\Report\Generator;

use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\Report;
use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

readonly class ProductCellFileReportGenerator implements ReportGeneratorInterface
{
    private const PATH = '/public/reports/';

    private const FILE_PREFIX = 'product_cell_';

    private string $projectDir;

    public function __construct(
        private ProductRepository $productRepository,
        ParameterBagInterface $params,
    ) {
        $this->projectDir = $params->get('kernel.project_dir');
    }

    public function generate(Report $report): Report
    {
        $orderedProductsIterator = $this->productRepository->getOrderedProductsInDatePeriodIterator($report->getDateFrom(), $report->getDateTo());

        $filesystem = new Filesystem();

        if (!$filesystem->exists($this->projectDir . self::PATH)) {
            $filesystem->mkdir($this->projectDir . self::PATH);
        }

        $fullPath = $this->projectDir . self::PATH . '/' . self::FILE_PREFIX . $report->getId();

        foreach ($orderedProductsIterator as $orderedProduct) {
            /** @var Product $orderedProduct */
            $data = [];
            foreach ($orderedProduct->getOrderItems() as $orderItem) {
                /* @var $orderItem OrderItem */
                $data[] = [
                    'product_name' => $orderedProduct->getName(),
                    'price' => $orderItem->getPrice(),
                    'amount' => $orderItem->getQuantity(),
                    'user' => [
                        'id' => $orderItem->getOrder()->getUserId(),
                    ],
                ];
            }

            $serializedData = json_encode($data, JSON_PRETTY_PRINT);

            file_put_contents($fullPath, $serializedData, FILE_APPEND);
        }

        $report->setFilePath($fullPath);

        return $report;
    }
}
