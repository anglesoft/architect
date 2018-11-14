<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Blueprints\Task;
use Closure;

class Feature extends Blueprint
{
    protected $stub = 'feature.stub';

    public function __construct(String $definition, Closure $callback = null, String $prefix = '', String $suffix = '')
    {
        // TODO config
        $prefix = '\\App\\Features\\' . $this->makeClassNameFromString($prefix);
        $suffix = 'Feature';

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

    public function getParameter() : String
    {
        foreach ($this->instructions as $instruction) {
            if (isset($instruction['expect']))
                return '$' . $instruction['expect'];
        }

        return '';
    }

    public function getBlock() : String
    {
        $block = '';

        $i = 1;
        $count = count($this->instructions);
        $prefix = '\\App\\Tasks\\';

        foreach ($this->instructions as $task) {
            $line = '';

            if (isset($task['return'])) {
                $line .= '$' . $task['return'] . ' = ';
            }

            $line .= '$this->run(' . $prefix . $task['class'] . '::class';

            if (isset($task['expect'])) {
                $line .= ', $'.$task['expect'];
            }

            $line .= ');';

            $return = '';

            $block .= <<<code
        $line

code;

            if (($i == $count) && isset($task['return'])) {
                $line = 'return $' . $task['return'] .';';

                $block .= <<<code

        $line
code;
            }

            $i++;
        }

        return $block;
    }

    // TODO: generate sub-tasks and tests
    // public function generate()
    // {
    //     foreach ($this->instructions as $instruction) {
    //         $blueprint = new Task($instruction['class']);
    //         $blueprint->method('run', $instruction);
    //         dump($blueprint);
    //     }
    //
    //     dump($this);
    // }
}
