<?php

namespace App\Tests\Feature\Product;

use App\Factory\Entity\ProductFactory;
use App\Message\Product\Measurement;
use App\Tests\Helpers\Helpers;
use App\Tests\TransactionWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ProductControllerTest extends TransactionWebTestCase
{
    use Helpers;

    #[DataProvider('indexDataProvider')]
    public function testIndex(array $createData, array $outputData): void
    {
        foreach ($createData as $productData) {
            ProductFactory::new()->create($productData);
        }

        $this->client->request('GET', '/api/products');

        $data = json_decode($this->client->getResponse()->getContent(), true);

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
                        'external_id' => 67890,
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
                        'external_id' => 67892,
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
                            'externalId' => 67892,
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
                            'externalId' => 67890,
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
                    ]
                ],
            ]
        ];
    }
}
