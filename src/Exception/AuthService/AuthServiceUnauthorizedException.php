<?php

namespace App\Exception\AuthService;

use Exception;

class AuthServiceUnauthorizedException extends Exception
{
    protected $message = 'User not found or token is invalid';
}
