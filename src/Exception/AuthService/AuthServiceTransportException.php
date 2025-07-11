<?php

namespace App\Exception\AuthService;

use Exception;

class AuthServiceTransportException extends Exception
{
    protected $message = 'Auth service unavailable';
}
