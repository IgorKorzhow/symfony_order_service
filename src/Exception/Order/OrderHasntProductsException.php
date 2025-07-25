<?php

namespace App\Exception\Order;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderHasntProductsException extends UnprocessableEntityHttpException
{

}

