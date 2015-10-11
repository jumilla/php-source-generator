<?php

use Jumilla\Generators\Php\ClassName;

class ClassNameTests extends TestCase
{
    public function test_constructor()
    {
        $object = new ClassName('foo');

        Assert::same('foo', $object->name());
    }

    public function test_toString()
    {
        $object = new ClassName('foo');

        Assert::same('foo::class', (string) $object);
    }
}
