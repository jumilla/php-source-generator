<?php

use Jumilla\Generators\Php\ConfigGenerator;
use Jumilla\Generators\Php\ClassName;

class ConfigGeneratorTests extends TestCase
{
    public function test_generateTextMethod()
    {
        $text = ConfigGenerator::generateText([
            'foo' => 'bar',
        ]);

        Assert::same("<?php

return [
    'foo' => 'bar',
];
", $text);
    }

    public function test_generateMethod_simpleArray()
    {
        $text = (new ConfigGenerator())->generate([
            null,
            false,
            'value',
            [],
            new ClassName('MyClass'),
            0,
        ]);

        Assert::same("<?php

return [
    null,
    false,
    'value',
    [
    ],
    MyClass::class,
    0,
];
", $text);
    }

    public function test_generateMethod_keyedArray()
    {
        $text = (new ConfigGenerator())->generate([
            'foo' => null,
            'bar' => false,
            'baz' => 'value',
            'qux' => [],
            'quux' => new ClassName('MyClass'),
            'corge' => 0,
        ]);

        Assert::same("<?php

return [
    'foo' => null,
    'bar' => false,
    'baz' => 'value',
    'qux' => [
    ],
    'quux' => MyClass::class,
    'corge' => 0,
];
", $text);
    }
}
