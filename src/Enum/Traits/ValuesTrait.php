<?php

declare(strict_types=1);

namespace App\Enum\Traits;

trait ValuesTrait
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
