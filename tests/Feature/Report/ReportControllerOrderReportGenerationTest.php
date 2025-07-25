<?php

declare(strict_types=1);

namespace App\Tests\Feature\Report;

use App\Tests\Helpers\Helpers;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ReportControllerOrderReportGenerationTest extends WebTestCase
{
    use Helpers;
    use Factories;

    #[NoReturn]
    public function testOrderReportGeneration(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/report/order-generation',
            server: ['HTTP_AUTHORIZATION' => 'ROLE_USER,ROLE_ADMIN', 'CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'],
            content: json_encode([
                'reportType' => 'product_celled_report',
                'dateFrom' => '12.12.2012',
                'dateTo' => '14.12.2026',
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(201);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('reportType', $data);
        $this->assertArrayHasKey('dateFrom', $data);
        $this->assertArrayHasKey('dateTo', $data);
        $this->assertArrayHasKey('filePath', $data);
    }

    public function testUnauthorized(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/report/order-generation');

        $this->assertResponseStatusCodeSame(401);
    }
}
