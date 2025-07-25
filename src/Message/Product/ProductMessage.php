<?php

declare(strict_types=1);

namespace App\Message\Product;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('kafka_product_consumer')]
class ProductMessage
{
    public function __construct(
        private int $id,
        private string $name,
        private ?string $description,
        private int $cost,
        private int $tax,
        private int $version,
        public Measurement $measurements,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function setTax(int $tax): void
    {
        $this->tax = $tax;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    public function getMeasurements(): Measurement
    {
        return $this->measurements;
    }

    public function setMeasurements(Measurement $measurements): void
    {
        $this->measurements = $measurements;
    }
}
