<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

trait Helpers
{
    protected function assertEqualsWithExcludedFields(array $actual, array $expected, array $excludedFields): void
    {
        foreach ($excludedFields as $path) {
            $this->removeByDotPath($actual, $path);
        }

        $this->assertEquals($expected, $actual);
    }

    protected function removeByDotPath(array &$array, string $path): void
    {
        $segments = explode('.', $path);
        $this->removeByDotPathRecursive($array, $segments);
    }

    protected function removeByDotPathRecursive(array &$array, array $segments): void
    {
        if (empty($segments)) {
            return;
        }

        $segment = array_shift($segments);

        if ($segment === '*') {
            foreach ($array as &$item) {
                if (is_array($item)) {
                    $this->removeByDotPathRecursive($item, $segments);
                }
            }
        } elseif (array_key_exists($segment, $array)) {
            if (empty($segments)) {
                unset($array[$segment]);
            } elseif (is_array($array[$segment])) {
                $this->removeByDotPathRecursive($array[$segment], $segments);
            }
        }
    }
}
