<?php

use Jumilla\Generators\FilesystemFactory;
use Jumilla\Generators\FileGenerator;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Adapter\Local as LocalAdapter;

class FactoryTests extends TestCase
{
    public function test_localFilesystem()
    {
        $filesystem = FilesystemFactory::local('sandbox/foo');

        Assert::true($filesystem instanceof FilesystemInterface, 'must FilesystemInterface');
        Assert::true($filesystem->getAdapter() instanceof LocalAdapter, 'must LocalAdapter');
        Assert::same('sandbox/foo/', $filesystem->getAdapter()->getPathPrefix());
    }

    public function test_makeFileGenerator()
    {
        $generator = FileGenerator::make('sandbox/foo', 'sandbox/bar');

        Assert::true($generator instanceof FileGenerator);
    }

    public function test_newFileGenerator()
    {
        $generator = new FileGenerator(FilesystemFactory::local('sandbox/foo'), FilesystemFactory::local('sandbox/bar'), (object) []);

        Assert::true($generator instanceof FileGenerator);
    }
}
