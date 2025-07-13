<?php

namespace App\Enum\Interfaces;

interface TypeByStringInterface
{
    public static function typeByString(string $type): mixed;
}
