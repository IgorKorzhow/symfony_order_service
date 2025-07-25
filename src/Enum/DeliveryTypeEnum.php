<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Interfaces\TypeByStringInterface;
use App\Enum\Interfaces\ValuesInterface;
use App\Enum\Traits\TypeByStringTrait;
use App\Enum\Traits\ValuesTrait;

enum DeliveryTypeEnum: string implements TypeByStringInterface, ValuesInterface
{
    use TypeByStringTrait;
    use ValuesTrait;

    case COURIER = 'courier';
    case PICKUP = 'pickup';
}
