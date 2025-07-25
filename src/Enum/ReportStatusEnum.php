<?php

declare(strict_types=1);

namespace App\Enum;

use App\Enum\Interfaces\TypeByStringInterface;
use App\Enum\Interfaces\ValuesInterface;
use App\Enum\Traits\TypeByStringTrait;
use App\Enum\Traits\ValuesTrait;

enum ReportStatusEnum: string implements TypeByStringInterface, ValuesInterface
{
    use TypeByStringTrait;
    use ValuesTrait;

    case CREATED = 'created';
    case SUCCESS = 'success';
    case ERROR = 'error';
}
