<?php

declare(strict_types=1);

namespace App\Exception\AuthService;

class AuthServiceTransportException extends \Exception
{
    protected $message = 'Auth service unavailable';
}
