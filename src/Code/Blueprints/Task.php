<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Closure;

class Task extends Blueprint
{
    /**
     * Reference to the stub file.
     *
     * @var string
     */
    protected $stub = 'task.stub';

    /**
     * Create a new task blueprint instance.
     *
     * @param string $description
     * @param Closure $callback
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(string $description, Closure $callback = null, string $prefix = '', string $suffix = '')
    {
        $prefix = config('architect.compiler.namespaces.tasks') . '\\' . $this->makeClassNameFromString($prefix);
        // $suffix = 'Task'; CONFIG

        parent::__construct($description, $callback, $prefix, $suffix);
    }

    private function get($key) : string
    {
        return $this->methods['run'][$key];
    }

    private function has($key) : bool
    {
        return isset($this->methods['run'][$key]);
    }

    public function getReturn() : string
    {
        if ( ! $this->has('return'))
            return '//';

        $return = $this->get('return');

        return "return \${$return};";
    }

    public function getProperties() : string
    {
        if ( ! $this->has('expect'))
            return '';

        $property = $this->get('expect');

        return "    protected \${$property};

";
    }

    public function getExpect() : string
    {
        if ( ! $this->has('expect'))
            return '';

        $expect = $this->get('expect');

        return "\${$expect}";
    }

    public function getConstruct() : string
    {
        if ( ! $this->has('expect'))
            return '';

        $property = $this->get('expect');


        return "    /**
     * Create a new task instance.
     *
     * @return void
     */
    public function __construct(\${$property})
    {
        \$this->{$property} = \${$property};
    }

";
    }

    public function getRun() : string
    {
        $return = $this->getReturn();

        return "    /**
     * Execute the task.
     *
     * @return mixed
     */
    public function run()
    {
        $return
    }";
    }
}
