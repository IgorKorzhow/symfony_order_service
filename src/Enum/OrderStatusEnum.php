<?php

namespace App\Enum;

use App\Enum\Interfaces\TypeByStringInterface;
use App\Enum\Interfaces\ValuesInterface;
use App\Enum\Traits\TypeByStringTrait;
use App\Enum\Traits\ValuesTrait;

enum OrderStatusEnum: string implements TypeByStringInterface, ValuesInterface
{
    use TypeByStringTrait;
    use ValuesTrait;

    case CREATED = 'created';

    case PAYED = 'payed';

    case WAITING_BUILDING = 'waiting_building';

    case BUILDING = 'building';

    case READY_FOR_PICKUP = 'ready_for_pickup';

    case DELIVERY = 'delivery';

    case RECEIVED = 'received';

    case CANCELLED = 'cancelled';

    public static function hasValue(string $type): bool
    {
        try {
            return (bool) self::from($type);
        } catch (\ValueError) {
            return false;
        }
    }
}
