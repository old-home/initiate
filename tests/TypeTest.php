<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests;

use Graywings\Instantiate\Tests\Sample\SampleAllowsNull;
use Graywings\Instantiate\Tests\Sample\SampleBool;
use Graywings\Instantiate\Tests\Sample\SampleChild;
use Graywings\Instantiate\Tests\Sample\SampleExtendAndImplement;
use Graywings\Instantiate\Tests\Sample\SampleFloat;
use Graywings\Instantiate\Tests\Sample\SampleInt;
use Graywings\Instantiate\Tests\Sample\SampleIntersection;
use Graywings\Instantiate\Tests\Sample\SampleMixed;
use Graywings\Instantiate\Tests\Sample\SampleParent;
use Graywings\Instantiate\Tests\Sample\SampleString;
use Graywings\Instantiate\Tests\Sample\SampleUnion;
use Graywings\Instantiate\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(Type::class)]
class TypeTest extends TestCase
{
    public function test_propertyTypeNotDefined()
    {
        $this->assertTrue(Type::matches(null, ''));
    }

    #[DataProvider('classNameAndMatchType')]
    public function test_matchNamedType(
        string $className,
        mixed $value
    )
    {
        $class = new ReflectionClass($className);
        $type = $class->getConstructor()?->getParameters()[0]->getType();
        $this->assertTrue(Type::matchNamedType($type, $value));
    }

    #[DataProvider('classNameAndUnmatchedType')]
    public function test_unmatchedNamedType(
        string $className,
        mixed $value
    )
    {
        $class = new ReflectionClass($className);
        $type = $class->getConstructor()?->getParameters()[0]->getType();
        $this->assertFalse(Type::matchNamedType($type, $value));
    }

    public function test_unionType()
    {
        $class = new ReflectionClass(SampleUnion::class);
        $type = $class->getConstructor()?->getParameters()[0]->getType();
        $this->assertTrue(Type::matches($type, ''));
        $this->assertTrue(Type::matches($type, 0));
    }

    public function test_intersectionType()
    {
        $class = new ReflectionClass(SampleIntersection::class);
        $type = $class->getConstructor()?->getParameters()[0]->getType();
        $this->assertTrue(Type::matches($type, new SampleExtendAndImplement()));
    }

    public static function classNameAndMatchType(): array
    {
        return [
            [SampleAllowsNull::class, null],
            [SampleMixed::class, null],
            [SampleMixed::class, true],
            [SampleMixed::class, 2.0],
            [SampleMixed::class, 1],
            [SampleMixed::class, 'hello'],
            [SampleBool::class, true],
            [SampleFloat::class, 2.0],
            [SampleInt::class, 1],
            [SampleString::class, 'hello'],
            [SampleParent::class, new SampleChild(1)]
        ];
    }

    public static function classNameAndUnmatchedType(): array
    {
        return [
            [SampleBool::class, 'hello'],
            [SampleFloat::class, 1],
            [SampleInt::class, 2.0],
            [SampleString::class, true],
            [SampleParent::class, new SampleString('hello')]
        ];
    }
}
