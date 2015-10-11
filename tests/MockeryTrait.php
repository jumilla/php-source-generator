<?php

trait MockeryTrait
{
    /**
     * @after
     */
    public function teardownMockery()
    {
        Mockery::close();
    }

    /**
     * @param string    $class
     * @param array     $overrides
     * @param overrides $callback
     *
     * @return \Mockery\MockInterface
     */
    protected function createMock($class, array $overrides = null, callable $callback = null)
    {
        // override all methods
        if ($overrides === null) {
            return Mockery::mock($class);
        }
        // override partial methods
        else {
            return Mockery::mock(
                $class.'['.implode(', ', $overrides).']',
                $callback ? call_user_func($callback) : []
            );
        }
    }
}
