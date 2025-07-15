<?php

namespace App\Factory\Entity;

use App\Entity\Product;
use App\Message\Product\Measurement;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Product::class;
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable    {
        return [
            'cost' => self::faker()->randomNumber(),
            'description' => self::faker()->text(),
            'external_id' => self::faker()->randomNumber(),
            'measurements' => new Measurement(
                self::faker()->randomNumber(),
                self::faker()->randomNumber(),
                self::faker()->randomNumber(),
                self::faker()->randomNumber()
            ),
            'name' => self::faker()->text(255),
            'tax' => self::faker()->randomNumber(),
            'version' => self::faker()->randomNumber(),
        ];
    }

        /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Product $product): void {})
        ;
    }
}
