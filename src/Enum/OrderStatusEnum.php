<?php

namespace App\Enum;

use App\Exception\UnknownEnumTypeException;
use ValueError;

enum OrderStatusEnum: string implements TypeByStringInterface, ValuesInterface
{
    case CREATED = 'created';

    case PAYED = 'payed';

    case WAITING_BUILDING = 'waiting_building';

    case BUILDING = 'building';

    case READY_FOR_PICKUP = 'ready_for_pickup';

    case DELIVERY = 'delivery';

    case RECEIVED = 'received';

    case CANCELLED = 'cancelled';

    /**
     * @throws UnknownEnumTypeException
     */
    public static function typeByString(string $type): OrderStatusEnum
    {
        try {
            return self::from($type);
        } catch (ValueError) {
            throw new UnknownEnumTypeException('Unknown enum type: ' . $type . ' for enum: ' . self::class);
        }
    }

    public static function hasValue(string $type): bool
    {
        try {
            return (bool) self::from($type);
        } catch (ValueError) {
            return false;
        }
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
