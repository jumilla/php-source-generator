<?php

use Jumilla\Generators\FileGenerator as Base;

class FileGenerator extends Base
{
    public function getOutbox()
    {
        return $this->outbox;
    }

    public function getStubbox()
    {
        return $this->stubbox;
    }

    public function getContext()
    {
        return $this->context;
    }
}
