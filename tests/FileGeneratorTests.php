<?php

use League\Flysystem\FileNotFoundException;

class FileGeneratorTests extends TestCase
{
    use MockeryTrait;

    public function setup()
    {
        $this->generator = FileGenerator::make('sandbox/out', 'sandbox/stub');
    }

    public function test_directoryMethod_once()
    {
        $sub = $this->generator->directory('foo');

        Assert::same('foo', $sub->getContext()->directory);
    }

    public function test_directoryMethod_twice()
    {
        $sub = $this->generator->directory('foo')->directory('bar');

        Assert::same('foo/bar', $sub->getContext()->directory);
    }

    public function test_directoryMethod_withClosure()
    {
        $sub = $this->generator->directory('bar', function ($generator) {
            $generator->file('baz')->blank();
        });

        Assert::same('bar', $sub->getContext()->directory);
        Assert::fileExists('sandbox/out/bar/baz');
    }

    public function test_directoryMethod_withInstanceMethod()
    {
        $mock = $this->createMock('callback');
        $mock->shouldReceive('baz')->once();
        $sub = $this->generator->directory('bar', [$mock, 'baz']);

        Assert::same('bar', $sub->getContext()->directory);
    }

    public function test_sourceDirectoryMethod()
    {
        $source1 = '<?php class Class1 {}';
        $source2 = '<?php class Class2 {}';

        $this->generator->getStubbox()->put('Class1.php', $source1);
        $this->generator->getStubbox()->put('sub/Class2.php', $source2);
        $this->generator->sourceDirectory('.');

        Assert::stringEqualsFile('sandbox/out/Class1.php', $source1);
        Assert::stringEqualsFile('sandbox/out/sub/Class2.php', $source2);
    }

    public function test_sourceDirectoryMethod_inDirectory()
    {
        $source1 = '<?php class Class1 {}';
        $source2 = '<?php class Class2 {}';
        $this->generator->getStubbox()->put('bar/Class1.php', $source1);
        $this->generator->getStubbox()->put('bar/sub/Class2.php', $source2);

        $this->generator->directory('bar', function ($generator) {
            $generator->sourceDirectory('.');
        });

        Assert::stringEqualsFile('sandbox/out/bar/Class1.php', $source1);
        Assert::stringEqualsFile('sandbox/out/bar/sub/Class2.php', $source2);
    }

    public function test_templateDirectoryMethod()
    {
        $template1 = '<?php class {$class1} {}';
        $template2 = '<?php class {$class2} {}';
        $this->generator->getStubbox()->put('Class1.php', $template1);
        $this->generator->getStubbox()->put('sub/Class2.php', $template2);

        $arguments = [
            'class1' => 'Class1',
            'class2' => 'Class2',
        ];
        $this->generator->templateDirectory('.', $arguments);

        Assert::stringEqualsFile('sandbox/out/Class1.php', '<?php class Class1 {}');
        Assert::stringEqualsFile('sandbox/out/sub/Class2.php', '<?php class Class2 {}');
    }

    public function test_templateDirectoryMethod_inDirectory()
    {
        $template1 = '<?php class {$class1} {}';
        $template2 = '<?php class {$class2} {}';
        $this->generator->getStubbox()->put('bar/Class1.php', $template1);
        $this->generator->getStubbox()->put('bar/sub/Class2.php', $template2);

        $arguments = [
            'class1' => 'Class1',
            'class2' => 'Class2',
        ];
        $this->generator->directory('bar', function ($generator) use ($arguments) {
            $generator->templateDirectory('.', $arguments);
        });

        Assert::stringEqualsFile('sandbox/out/bar/Class1.php', '<?php class Class1 {}');
        Assert::stringEqualsFile('sandbox/out/bar/sub/Class2.php', '<?php class Class2 {}');
    }

    public function test_keepDirectoryMethod_noArgument()
    {
        $this->generator->keepDirectory('foo');

        Assert::stringEqualsFile('sandbox/out/foo/.gitkeep', '');
    }

    public function test_keepDirectoryMethod_withFilename()
    {
        $this->generator->keepDirectory('foo', 'bar');

        Assert::stringEqualsFile('sandbox/out/foo/bar', '');
    }

    public function test_fileMethod()
    {
        $generator = $this->generator->file('foo');

        Assert::same('foo', $generator->getContext()->file);
    }

    public function test_fileMethod_twice()
    {
        $generator = $this->generator->file('foo')->file('bar');

        Assert::same('bar', $generator->getContext()->file);
    }

    public function test_existsMethod_exists()
    {
        $this->generator->file('foo')->blank();

        $exists = $this->generator->exists('foo');

        Assert::true($exists);
    }

    public function test_existsMethod_noExists()
    {
        $exists = $this->generator->exists('foo');

        Assert::false($exists);
    }

    public function test_blankMethod()
    {
        $this->generator->file('foo')->blank();

        Assert::stringEqualsFile('sandbox/out/foo', '');
    }

    public function test_textMethod_noArguments()
    {
        $this->generator->file('foo')->text('bar baz');

        Assert::stringEqualsFile('sandbox/out/foo', 'bar baz');
    }

    public function test_textMethod_withArguments()
    {
        $this->generator->file('foo')->text('bar {$baz}', ['baz' => 'BBAAZZ']);

        Assert::stringEqualsFile('sandbox/out/foo', 'bar BBAAZZ');
    }

    public function test_jsonMethod()
    {
        $arguments = ['type' => 'bar', 'path' => 'baz'];
        $this->generator->file('foo')->json($arguments);

        Assert::stringEqualsFile('sandbox/out/foo', json_encode($arguments, JSON_PRETTY_PRINT));
    }

    public function test_sourceMethod()
    {
        $source = '<?php class Class1 {}';
        $this->generator->getStubbox()->put('Class1.php', $source);

        $this->generator->file('bar')->source('Class1.php');

        Assert::stringEqualsFile('sandbox/out/bar', $source);
    }

    public function test_sourceMethod_sourceMissing()
    {
        try {
            $this->generator->file('bar')->source('Class1.php');
            Assert::failure();
        } catch (FileNotFoundException $ex) {
            Assert::success();
        }
    }

    public function test_templateMethod()
    {
        $template = '<?php class {$class1} { const VALUES = [{$values}] }';
        $this->generator->getStubbox()->put('Class1.php', $template);

        $arguments = [
            'class1' => 'Class1',
            'values' => [1, 4, 9],
        ];
        $this->generator->file('bar')->template('Class1.php', $arguments);

        Assert::stringEqualsFile('sandbox/out/bar', '<?php class Class1 { const VALUES = [1, 4, 9] }');
    }

    public function test_templateMethod_templateMissing()
    {
        $arguments = [
            'class1' => 'Class1',
        ];

        try {
            $this->generator->file('bar')->template('Class1.php', $arguments);
            Assert::failure();
        } catch (FileNotFoundException $ex) {
            Assert::success();
        }
    }

    public function test_gitKeepFileMethod_noArgument()
    {
        $this->generator->gitKeepFile();

        Assert::stringEqualsFile('sandbox/out/.gitkeep', '');
    }

    public function test_gitKeepFileMethod_withFilename()
    {
        $this->generator->gitKeepFile('foo');

        Assert::stringEqualsFile('sandbox/out/foo', '');
    }

    public function test_phpBlankFileMethod_withFilename()
    {
        $this->generator->phpBlankFile('foo');

        Assert::stringEqualsFile('sandbox/out/foo', '<?php

');
    }

    public function test_phpConfigFileMethod_withFilename_blank()
    {
        $this->generator->phpConfigFile('foo');

        Assert::stringEqualsFile('sandbox/out/foo', '<?php

return [
];
');
    }

    public function test_phpConfigFileMethod_withFilename_andArgument()
    {
        $this->generator->phpConfigFile('foo', [
            'bar' => 'baz',
            'qux' => 'quxx',
        ]);

        Assert::stringEqualsFile('sandbox/out/foo', "<?php

return [
    'bar' => 'baz',
    'qux' => 'quxx',
];
");
    }

    public function test_phpSourceFileMethod_withFilename_andSource()
    {
        $this->generator->phpSourceFile('foo', 'bar');

        Assert::stringEqualsFile('sandbox/out/foo', '<?php

bar
');
    }

    public function test_phpSourceFileMethod_withFilename_andSource_andNamespace()
    {
        $this->generator->phpSourceFile('foo', 'bar', 'baz');

        Assert::stringEqualsFile('sandbox/out/foo', '<?php

namespace baz;

bar
');
    }

    public function test_sourceFileMethod()
    {
        $source = '<?php class Class1 {}';
        $this->generator->getStubbox()->put('Class1.php', $source);

        $arguments = [
            'class1' => 'Class1',
        ];
        $this->generator->sourceFile('Class1.php', $arguments);

        Assert::stringEqualsFile('sandbox/out/Class1.php', '<?php class Class1 {}');
    }

    public function test_templateFileMethod()
    {
        $template = '<?php class {$class1} {}';
        $this->generator->getStubbox()->put('Class1.php', $template);

        $arguments = [
            'class1' => 'Class1',
        ];
        $this->generator->templateFile('Class1.php', $arguments);

        Assert::stringEqualsFile('sandbox/out/Class1.php', '<?php class Class1 {}');
    }
}
