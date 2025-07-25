<?php

declare(strict_types=1);

namespace App\Exception\AuthService;

class AuthServiceUnauthorizedException extends \Exception
{
    protected $message = 'User not found or token is invalid';
}
