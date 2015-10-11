<?php

use Jumilla\Generators\FilesystemFactory;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @after
     */
    public function teardownFilesystem()
    {
        FilesystemFactory::local('.')->deleteDir('sandbox');
    }
}
