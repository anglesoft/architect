<?php

namespace Angle\Architect\Code\Blueprints;

use Closure;
use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Blueprints\Task;
use Angle\Architect\Code\Blueprints\Test;

class Feature extends Blueprint
{
    /**
     * Reference to the stub file.
     *
     * @var string
     */
    protected $stub = 'feature.stub';

    /**
     * Creates a new blueprint
     *
     * @todo Allow to configure pre/suffixes
     * @param string $description
     * @param Closure $callback
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(string $description, Closure $callback = null, string $prefix = '', string $suffix = '')
    {
        // TODO config
        $prefix = '\\App\\Features\\' . $this->makeClassNameFromString($prefix);
        // $suffix = 'Feature'; // CONFIG

        parent::__construct($description, $callback, $prefix, $suffix);
    }

    /**
     * Adds task to instructions.
     *
     * @param  string    $task
     * @return \Angle\Architect\Code\Blueprint
     */
    public function task(string $task) : Blueprint
    {
        $this->pushPreviousInstruction();

        $this->method = ['name' => 'handle'];

        $this->instruction['class'] = $this->makeClassNameFromString($task, 'Task'); // TODO config

        return $this;
    }

    /**
     * Alias to the task method.
     *
     * @param  string    $task
     * @return \Angle\Architect\Code\Blueprint
     */
    public function run(string $task) : Blueprint
    {
        return $this->task($task);
    }

    /**
     * Alias to the task method.
     *
     * @param  string    $task
     * @return \Angle\Architect\Code\Blueprint
     */
    public function will(string $task) : Blueprint
    {
        return $this->task($task);
    }

    /**
     * Formats parameter as string.
     *
     * @return string
     */
    public function getParameter() : string
    {
        foreach ($this->instructions as $instruction) {
            if (isset($instruction['expect']))
                return '$' . $instruction['expect'];
        }

        return '';
    }

    /**
     * Compose the feature handle method code block.
     *
     * @return string $block
     */
    public function getBlock() : string
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

    /**
     * Registers blueprints
     *
     * @return void
     */
    public function registerBlueprints() : void
    {
        $test = (new Test($this->getName(), null, 'Feature'))
            ->test('handle')
            ->use($this);

        foreach ($this->instructions as $instruction) {
            $task = (new Task($instruction['class']))->method('run', $instruction);
            $this->blueprint($task);

            $test->test($instruction['class'], $instruction)
                ->use($task);
        }

        $this->blueprint($test);
    }
}
