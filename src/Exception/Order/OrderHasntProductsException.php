<?php

declare(strict_types=1);

namespace App\Exception\Order;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderHasntProductsException extends UnprocessableEntityHttpException
{
}
