<?php

declare(strict_types=1);

namespace App\Tests\Feature\Product;

use App\Factory\Entity\ProductFactory;
use App\Message\Product\Measurement;
use App\Tests\Helpers\Helpers;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProductControllerIndexTest extends WebTestCase
{
    use Helpers;
    use Factories;

    #[DataProvider('indexDataProvider')]
    public function testIndex(array $createData, array $outputData): void
    {
        $client = self::createClient();

        foreach ($createData as $productData) {
            ProductFactory::new()->create($productData);
        }

        $client->request('GET', '/api/products');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEqualsWithExcludedFields($data, $outputData, ['data.*.id', 'data.*.createdAt', 'data.*.orderItems']);
    }

    public static function indexDataProvider(): array
    {
        return [
            [
                'createData' => [
                    [
                        'cost' => 12345,
                        'description' => 'This is a sample product description.',
                        'measurements' => new Measurement(
                            10,
                            20,
                            30,
                            40
                        ),
                        'name' => 'Sample Product Name',
                        'tax' => 15,
                        'version' => 1,
                    ],
                    [
                        'cost' => 123452,
                        'description' => 'This is a sample product description.',
                        'measurements' => new Measurement(
                            10,
                            20,
                            30,
                            40
                        ),
                        'name' => 'Sample Product Name',
                        'tax' => 15,
                        'version' => 1,
                    ],
                ],
                'outputData' => [
                    'page' => 1,
                    'perPage' => 10,
                    'total' => 2,
                    'totalPages' => 1,
                    'data' => [
                        [
                            'cost' => 123452,
                            'description' => 'This is a sample product description.',
                            'measurements' => [
                                'weight' => 10,
                                'height' => 20,
                                'width' => 30,
                                'length' => 40,
                            ],
                            'name' => 'Sample Product Name',
                            'tax' => 15,
                            'version' => 1,
                        ],
                        [
                            'cost' => 12345,
                            'description' => 'This is a sample product description.',
                            'measurements' => [
                                'weight' => 10,
                                'height' => 20,
                                'width' => 30,
                                'length' => 40,
                            ],
                            'name' => 'Sample Product Name',
                            'tax' => 15,
                            'version' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testQueryParams(): void
    {
        $client = self::createClient();

        ProductFactory::new()->many(1)->create();

        $client->request(
            method: 'GET',
            uri: '/api/products',
            parameters: ['perPage' => 1, 'page' => 2],
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame([], $data['data']);
        $this->assertSame(1, $data['totalPages']);
        $this->assertSame(2, $data['page']);
        $this->assertSame(1, $data['perPage']);
    }
}
