<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Blueprints\Task;
use Angle\Architect\Code\Blueprints\Test;
use Closure;

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
     * @param string $description
     * @param Closure $callback
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(string $description, Closure $callback = null, string $prefix = '', string $suffix = '')
    {
        $prefix = config('architect.compiler.namespaces.features') . '\\' . $this->makeClassNameFromString($prefix);
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

    public function getUses() : array
    {
        $this->uses = [];

        $blueprints = $this->getBlueprints();
        $prefix = config('architect.compiler.namespaces.tasks');

        foreach ($blueprints as $blueprint) {
            if ($blueprint->getNamespace() == $prefix)
                $this->uses[] = $prefix . '\\' . $blueprint->getName();
        }

        return $this->uses;
    }

    public function getUse() : string
    {
        $string = '';

        $uses = $this->getUses();

        if (count($uses) == 0)
            return $string;

        $string .= "\n";

        foreach ($uses as $class) {
            $string .= "use $class;";

            if ($uses != end($uses) && count($uses) > 1)
                $string .= "\n";
        }

        return $string;
    }

    /**
     * Compose the feature handle method code block.
     *
     * @return string $block
     */
    public function getBlock() : string
    {
        $block = '';

        foreach ($this->instructions as $task) {
            $line = '';
            $return = isset($task['return']) ? $task['return'] : null;
            $expect = isset($task['expect']) ? $task['expect'] : null;
            $class = $task['class'];
            $last = $task == end($this->instructions);

            if ($return)
                $line .= '$' . $return . ' = ';

            $line .= '$this->run(' . $class . '::class';

            if ($expect)
                $line .= ', [\'' . $expect . '\' => $' . $expect . ']';

            $line .= ');';

            $block .= <<<code
        $line

code;

            if ($last && $return) {
                $line = 'return $' . $return .';';
                $block .= <<<code

        $line
code;
            }
        }

        return $block;
    }

    /**
     * Registers blueprints before compilation
     *
     * @return void
     */
    public function compose() : Blueprint
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

        return parent::compose();
    }
}
