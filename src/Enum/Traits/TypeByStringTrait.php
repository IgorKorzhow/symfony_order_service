<?php

namespace App\Enum\Traits;

use App\Enum\OrderStatusEnum;
use App\Exception\UnknownEnumTypeException;

trait TypeByStringTrait
{
    /**
     * @throws UnknownEnumTypeException
     */
    public static function typeByString(string $type): static
    {
        try {
            return self::from($type);
        } catch (\ValueError) {
            throw new UnknownEnumTypeException('Unknown enum type: '.$type.' for enum: '.self::class);
        }
    }
}
