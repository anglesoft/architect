<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Closure;
use Illuminate\Support\Str;

class Test extends Blueprint
{
    /**
     * Reference to the stub file.
     *
     * @var string
     */
    protected $stub = 'test.stub';

    /**
     * Create a new test blueprint instance.
     *
     * @todo Allow to configure pre/suffixes
     * @param string $name
     * @param Closure $callback
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(string $name, Closure $callback = null, string $prefix = '', string $suffix = '')
    {
        parent::__construct($name, $callback, $prefix, $suffix);

        $this->file = 'tests/' . Str::studly($prefix) . '/' . $this->getName() . 'Test' . '.php';
        $this->path = base_path($this->getFileName());
        $this->namespace = 'Tests\\' . Str::studly($prefix);
    }

    /**
     * Adds a test method to the blueprint.
     *
     * @param string $method
     * @param array $options
     * @return \Angle\Architect\Code\Blueprint
     */
    public function test(string $method, array $options = []) : Blueprint
    {
        return $this->method('test' . Str::studly($method), $options);
    }

    /**
     * Compiles the test code
     *
     * @return string
     */
    public function getTest() : string
    {
        return '';
    }
}
