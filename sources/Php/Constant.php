<?php

namespace Jumilla\Generators\Php;

class Constant
{
    /**
     * Constant name.
     *
     * @var string
     */
    protected $name;

    /**
     * the Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = trim($name, '\\');
    }

    /**
     * Get constant name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get class string.
     * use PHP 5.5 notation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
