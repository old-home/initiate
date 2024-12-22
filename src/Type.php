<?php

declare(strict_types=1);

namespace Graywings\Instantiate;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class Type
{
    public static function matches(?ReflectionType $type, mixed $value): bool
    {
        if ($type === null) {
            return true;
        }
        if ($type instanceof ReflectionNamedType) {
            return self::matchNamedType($type, $value);
        } elseif($type instanceof ReflectionUnionType) {
            return array_any($type->getTypes(), function ($type) use ($value) {
                return self::matches($type, $value);
            });
        } elseif($type instanceof ReflectionIntersectionType) {
            return array_all($type->getTypes(), function ($type) use ($value) {
                return self::matches($type, $value);
            });
        }
        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    public static function matchNamedType(ReflectionNamedType $type, mixed $value): bool
    {
        if ($type->allowsNull() && $value === null) {
            return true;
        } elseif($type->isBuiltin()) {
            return match($type->getName()) {
                'string' => is_string($value),
                'int' => is_int($value),
                'float' => is_float($value),
                'bool' => is_bool($value),
                'mixed' => true,
                default => false
            };
        } else {
            return is_a($value, $type->getName());
        }
    }
}
