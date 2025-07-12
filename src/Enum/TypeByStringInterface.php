<?php

namespace App\Enum;

interface TypeByStringInterface
{
    public static function typeByString(string $type): mixed;
}
