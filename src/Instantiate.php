<?php

declare(strict_types=1);

namespace Graywings\Instantiate;

use Closure;
use JsonException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use stdClass;

class Instantiate
{
    /**
     *
     * Dynamically instantiates an object of the specified class name.
     *
     * This function generates an instance of the specified class based on the given array or stdClass object.
     *
     * @param array<mixed> $value An array or stdClass object containing the values. For the class properties.
     * @param string $className The name of the class to instantiate. Must be a class-string type.
     * @return mixed Returns an instance of the specified class on success, or null on failure.
     *
     * @throws InstantiateException Thrown if the specified class does not exist.
     *
     *  Example usage of this function:
     *  ```
     *  $data = ['prop1' => 'value1', 'prop2' => 'value2'];
     *  $object = instantiate($data, MyClass::class);
     *  ```
     */
    public static function array(
        array  $value,
        string $className
    ): mixed {
        return self::instantiateMain(
            $value,
            $className,
            fn(string $key, array $value) => $value[$key],
            fn(array $value, string $className) => self::array($value, $className)
        );
    }

    public static function stdClass(
        stdClass $object,
        string $className
    ): mixed {
        return self::instantiateMain(
            $object,
            $className,
            fn(string $key, stdClass $object) => $object->$key,
            fn(stdClass $object, string $className) => self::stdClass($object, $className)
        );
    }

    /**
     * @param string $json
     * @param class-string $className
     * @param int<0, 2147483647> $depth
     * @return mixed
     * @throws InstantiateException
     */
    public static function json(
        string $json,
        string $className,
        int $depth = 512
    ): mixed
    {
        try {
            if ($depth < 1) {
                throw new JsonException("\$depth: $depth is not natural numbers");
            }
            $decoded = json_decode($json, false, $depth, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (JsonException $e) {
            throw new InstantiateException(
                "JSON decode error: " . $e->getMessage(),
                -1,
                $e
            );
        }
        return self::stdClass($decoded, $className);
    }

    /**
     * @param array<mixed> $value
     * @param string $className
     * @param Closure $extract
     * @return mixed
     */
    private static function instantiateMain(
        array|stdClass $value,
        string $className,
        Closure $extract,
        Closure $callback
    ): mixed {
        $reflectionClass = self::getReflectionClass($className);
        $isShortable = self::classIsShortable($className);
        $parameters = self::getParametersFromClassName($className);
        $arguments = [];
        $errors = [];
        foreach ($parameters as $parameter) {
            $reflectionAttributes = $parameter->getAttributes(PropertyName::class);
            $type = $parameter->getType();

            if ($reflectionAttributes) {
                $propertyName = $reflectionAttributes[0]->newInstance()->name;
            } else {
                $propertyName = $parameter->getName();
            }

            $extracted = $extract($propertyName, $value);

            if (is_null($type) || !is_array($extracted) && !$extracted instanceof stdClass) {
                $argument = $extracted;
            } else {
                try {
                    /** @var ReflectionNamedType $type */
                    $argument = $callback($extracted, $type->getName());
                } catch (InstantiateArgumentsException | InstantiateException $e) {
                    $errors[] = [
                        'name' => $parameter->getName(),
                        'value' => $e->errors
                    ];
                }
            }

            if (!Type::matches($type, $argument)) {
                $errors[] = [
                    'name' => $parameter->getName(),
                    'value' => $extracted
                ];
            } else {
                $arguments[] = $argument;
            }
        }
        if (count($errors) > 0) {
            throw new InstantiateArgumentsException($errors);
        }
        return new $className(...$arguments);
    }

    private static function getReflectionClass(string $className): ReflectionClass
    {
        if (class_exists($className)) {
            return new ReflectionClass($className);
        } else {
            throw new InstantiateException(
                "Class \"$className\" does not exist",
                -1
            );
        }
    }

    /**
     * @param ReflctionClass $reflectionClass
     * @return boolean
     */
    private static function classIsShortable(ReflectionClass $reflectionClass): bool
    {
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return ReflectionParameter[]
     */
    private static function getParametersFromClassName(ReflectionClass $reflectionClass): array
    {
        $parameters = $reflectionClass->getConstructor()?->getParameters();
        $className = $reflectionClass->getName();
        if ($parameters === null) {
            throw new InstantiateException(
                "Constructor isn't defined for \"$className\"",
                -1
            );
        }
        return $parameters;
    }
}
