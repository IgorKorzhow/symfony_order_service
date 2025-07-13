<?php

namespace App\Enum;

use App\Enum\Interfaces\TypeByStringInterface;
use App\Enum\Interfaces\ValuesInterface;
use App\Enum\Traits\TypeByStringTrait;
use App\Enum\Traits\ValuesTrait;

enum ReportTypeEnum: string implements TypeByStringInterface, ValuesInterface
{
    use TypeByStringTrait;
    use ValuesTrait;

    case ORDER_REPORT = 'order_report';
}
