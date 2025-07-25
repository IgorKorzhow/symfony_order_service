<?php

declare(strict_types=1);

namespace App\Helpers;

final readonly class ArrayHelpers
{
    public static function pluck(array $array, string $key): array
    {
        return array_map(function ($item) use ($key) {
            if (is_array($item)) {
                return $item[$key] ?? null;
            }
            if (is_object($item)) {
                $getMethodName = 'get' . ucfirst($key);

                return $item->{$key} ?? $item->$getMethodName() ?? null;
            }

            return null;
        }, $array);
    }

    public static function groupBy(array $array, string $property): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!property_exists($item, $property)) {
                throw new \InvalidArgumentException("Property {$property} does not exist in object");
            }

            $getMethodName = 'get' . ucfirst($property);

            $key = $item->{$property} ?? $item->$getMethodName() ?? null;

            if (!isset($result[$key])) {
                $result[$key] = [];
            }

            $result[$key][] = $item;
        }

        return $result;
    }

    public static function first(array $array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }

            return reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }
}
