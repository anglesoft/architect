<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Closure;

class Task extends Blueprint
{
    protected $stub = 'task.stub';

    public function __construct(String $definition, Closure $callback = null, String $prefix = '', String $suffix = '')
    {
        // TODO config
        $prefix = '\\App\\Tasks\\' . $this->makeClassNameFromString($prefix);
        $suffix = 'Task';

        parent::__construct($definition, $callback, $prefix, $suffix);
    }

    public function task(String $task) : Blueprint
    {
        $this->pushPreviousInstruction();

        $this->method = ['name' => 'handle'];

        $this->instruction['class'] = $this->makeClassNameFromString($task, 'Task'); // TODO config

        return $this;
    }

    public function run(String $task) : Blueprint
    {
        return $this->task($task);
    }

    public function generate()
    {
        dump($this);

        // foreach ($this->methods as $method) {
        //     dump($method);
        // }
    }
}
