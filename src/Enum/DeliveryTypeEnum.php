<?php

namespace App\Enum;

use App\Exception\UnknownEnumTypeException;

enum DeliveryTypeEnum: string implements TypeByStringInterface, ValuesInterface
{
    case COURIER = 'courier';
    case PICKUP = 'pickup';

    /**
     * @throws UnknownEnumTypeException
     */
    public static function typeByString(string $type): DeliveryTypeEnum
    {
        return match ($type) {
            'courier' => DeliveryTypeEnum::COURIER,
            'pickup' => DeliveryTypeEnum::PICKUP,
            default => throw new UnknownEnumTypeException('Unknown enum type:' . $type . ' for enum: ' . DeliveryTypeEnum::class),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
