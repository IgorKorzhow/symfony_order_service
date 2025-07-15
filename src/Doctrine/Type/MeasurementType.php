<?php

namespace App\Doctrine\Type;

use App\Message\Product\Measurement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class MeasurementType extends JsonType
{
    const NAME = 'measurement';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof Measurement) {
            return parent::convertToDatabaseValue($value, $platform);
        }

        return parent::convertToDatabaseValue([
            'weight' => $value->getWeight(),
            'height' => $value->getHeight(),
            'width' => $value->getWidth(),
            'length' => $value->getLength(),
        ], $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Measurement
    {
        $data = parent::convertToPHPValue($value, $platform);

        if ($data === null) {
            return null;
        }

        return new Measurement(
            $data['weight'] ?? 0,
            $data['height'] ?? 0,
            $data['width'] ?? 0,
            $data['length'] ?? 0,
        );
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
