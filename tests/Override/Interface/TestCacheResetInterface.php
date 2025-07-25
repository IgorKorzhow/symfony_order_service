<?php

declare(strict_types=1);

namespace App\Tests\Override\Interface;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ResetInterface;

interface TestCacheResetInterface extends CacheInterface, ResetInterface
{
}
