<?php

declare(strict_types=1);

namespace App\Enum\Interfaces;

interface TypeByStringInterface
{
    public static function typeByString(string $type): mixed;
}
