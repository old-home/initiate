<?php

namespace Graywings\Instantiate\Tests;

use Graywings\Instantiate\Instantiate;
use Graywings\Instantiate\InstantiateException;
use Graywings\Instantiate\PropertyName;
use Graywings\Instantiate\Tests\Sample\SampleNoConstructor;
use Graywings\Instantiate\Tests\Sample\SampleParent;
use Graywings\Instantiate\Tests\Sample\SamplePropertyName;
use Graywings\Instantiate\Tests\Sample\SamplePropertyNameDuplicated;
use Graywings\Instantiate\Tests\Sample\SampleString;
use Graywings\Instantiate\Tests\Sample\SampleUser\SampleUser;
use Graywings\Instantiate\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Instantiate::class)]
#[CoversClass(Type::class)]
#[CoversClass(PropertyName::class)]
class InstantiateTest extends TestCase
{
    public function test_instantiate()
    {
        $instance = Instantiate::array(
            [
                'name' => 'John Doe',
            ],
            SampleString::class
        );
        $this->assertInstanceOf(SampleString::class, $instance);
        $this->assertSame('John Doe', $instance->name);
    }

    public function test_instantiateParent()
    {
            $instance = Instantiate::array(
                [
                    'value' => [
                        'value' => 100
                    ]
                ],
                SampleParent::class
            );
        $this->assertInstanceOf(SampleParent::class, $instance);
        $this->assertSame(100, $instance->value->value);
    }

    public function test_instantiateUser()
    {
        $id = 'abcdef-01234-56789';
        $email = 'taira.terashima@example.com';

        $user = Instantiate::array(
            [
                'id' => [
                    'value' => $id
                ],
                'email' => [
                    'value' => $email
                ]
            ],
            SampleUser::class
        );
        $this->assertInstanceOf(SampleUser::class, $user);
        $this->assertSame($id, $user->id->value);
        $this->assertSame($email, $user->email->value);
    }

    public function test_instantiateWithPropertyName()
    {
        $samplePropertyName = Instantiate::array([
            'name' => 'Taira Terashima'
        ], SamplePropertyName::class);
        $this->assertSame('Taira Terashima', $samplePropertyName->value);
    }

    public function test_instantiateUserFromStdClass()
    {
        $id = 'abcdef-01234-56789';
        $email = 'taira.terashima@example.com';
        $stdClassUser = new stdClass;
        $stdClassUser->id = new stdClass;
        $stdClassUser->email = new stdClass;
        $stdClassUser->id->value = $id;
        $stdClassUser->email->value = $email;

        $user = Instantiate::stdClass($stdClassUser, SampleUser::class);
        $this->assertInstanceOf(SampleUser::class, $user);
        $this->assertSame($id, $user->id->value);
        $this->assertSame($email, $user->email->value);
    }

    public function test_instantiateUserFromJson()
    {
        $id = 'abcdef-01234-56789';
        $email = 'taira.terashima@example.com';
        $json = file_get_contents('tests/json/sample-user.json');
        $user = Instantiate::json($json, SampleUser::class);
        $this->assertInstanceOf(SampleUser::class, $user);
        $this->assertSame($id, $user->id->value);
        $this->assertSame($email, $user->email->value);
    }

    public function test_instantiateFromJsonInvalidDepth()
    {
        $json = file_get_contents('tests/json/sample-user.json');
        $this->expectException(InstantiateException::class);
        $this->expectExceptionMessage('JSON decode error: $depth: -1 is not natural numbers');
        Instantiate::json($json, SampleUser::class, -1);
    }

    public function test_instantiateFromNullJson()
    {
        $json = file_get_contents('tests/json/null.json');
        $this->expectException(InstantiateException::class);
        $this->expectExceptionMessage('JSON decode error: Syntax error');
        Instantiate::json($json, SampleUser::class);
    }

    public function test_instantiateFromInvalidJson()
    {
        $json = file_get_contents('tests/json/invalid.json');
        $this->expectException(InstantiateException::class);
        $this->expectExceptionMessage('JSON decode error: Syntax error');
        Instantiate::json($json, SampleUser::class);
    }


    public function test_instantiateNoConstructor()
    {
        $this->expectException(InstantiateException::class);
        $this->expectExceptionCode(-1);
        $this->expectExceptionMessage("Constructor isn't defined for \"Graywings\\Instantiate\\Tests\\Sample\\SampleNoConstructor\"");
        Instantiate::array([], SampleNoConstructor::class);
    }

    public function test_instantiateNotExistClass()
    {
        $this->expectException(InstantiateException::class);
        $this->expectExceptionCode(-1);
        $this->expectExceptionMessage("Class \"Graywings\\Instantiate\\Tests\\SampleNotDefined\" does not exist");
        Instantiate::array([], SampleNotDefined::class);
    }
}
