<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Report;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\Report;
use App\Repository\ProductRepository;
use App\Service\Report\Generator\ProductCellFileReportGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

class ProductCellFileReportGeneratorTest extends TestCase
{
    private ProductCellFileReportGenerator $generator;
    private ProductRepository $productRepository;
    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir();

        $this->productRepository = $this->createMock(ProductRepository::class);
        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('get')
            ->with('kernel.project_dir')
            ->willReturn($this->projectDir);

        $this->generator = new ProductCellFileReportGenerator(
            $this->productRepository,
            $parameterBag
        );
    }

    public function testGenerateReport(): void
    {
        $uuid = Uuid::v4();

        $report = new Report();
        $report->setId($uuid);
        $dateFrom = new \DateTimeImmutable('2023-01-01');
        $dateTo = new \DateTimeImmutable('2023-01-31');
        $report->setDateFrom($dateFrom);
        $report->setDateTo($dateTo);

        $product = new Product();
        $product->setName('Test Product');

        $order = new Order();
        $order->setUserId(123);

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setOrder($order);
        $orderItem->setPrice(100.50);
        $orderItem->setQuantity(2);

        $product->addOrderItem($orderItem);

        $this->productRepository
            ->expects($this->once())
            ->method('getOrderedProductsInDatePeriodIterator')
            ->with($dateFrom, $dateTo)
            ->willReturn(new \ArrayIterator([$product]));

        $result = $this->generator->generate($report);

        $expectedPath = $this->projectDir . '/public/reports/product_cell_' . $uuid->toString();
        $this->assertEquals($expectedPath, $result->getFilePath());

        $fileContent = file_get_contents($expectedPath);
        $this->assertJson($fileContent);

        $data = json_decode($fileContent, true);
        $this->assertCount(1, $data);
        $this->assertEquals('Test Product', $data[0]['product_name']);
        $this->assertEquals(100.5, $data[0]['price']);
        $this->assertEquals(2, $data[0]['amount']);
        $this->assertEquals(123, $data[0]['user']['id']);

        // Cleanup
        unlink($expectedPath);
    }

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $reportDir = $this->projectDir . '/public/reports';
        if ($filesystem->exists($reportDir)) {
            $filesystem->remove($reportDir);
        }
    }
}
