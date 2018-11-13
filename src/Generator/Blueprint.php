<?php

namespace Angle\Architect\Generator;

use Closure;

class Blueprint
{
    protected $feature = '';
    protected $tasks = [];
    // protected $variables = [];
    protected $prefix = '';
    protected $task = []; // Current task

    public function __construct($description, Closure $callback = null, $prefix = '')
    {
        $this->prefix = $this->makeClassName($prefix);
        $this->feature = $this->makeFeatureClassNameFromString($description);

        if ( ! is_null($callback)) {
            $callback($this);
        }

        $this->pushPreviousTask();
    }

    private function pushPreviousTask()
    {
        if ( ! empty($this->task)) // Push previous task
            $this->tasks[] = $this->task;
    }

    public function task($task)
    {
        $this->pushPreviousTask();

        $this->task['class'] = $this->makeTaskClassNameFromString($task);

        return $this;
    }

    public function will($task) // Alias that might desappear
    {
        return $this->task($task);
    }

    public function expect($parameter) // TODO expect array;
    {
        $this->task['expect'] = $parameter; // TODO makeVariableNameFromString

        return $this;
    }

    public function return($parameter) // TODO return list() multiple parameters
    {
        $this->task['return'] = $parameter; // TODO makeVariableNameFromString

        return $this;
    }

    private function makeFeatureClassNameFromString(String $string)
    {
        $feature = $this->makeClassName($string, 'feature'); // Todo add to config
        $feature = $this->prefix($feature, $this->prefix);
        $feature = $this->appendFeaturesPrefix($feature);

        return $feature;
    }

    private function makeTaskClassNameFromString(String $string)
    {
        $task = $this->makeClassName($string, 'task'); // Todo add to config
        $task = $this->prefix($task, $this->prefix);
        $task = $this->appendTasksPrefix($task);

        return $task;
    }

    private function isClassName(String $string)
    {
        return str_contains('\\', $string);
    }

    private function makeClassName(String $string, String $suffix = '')
    {
        if ($this->isClassName($string)) { // Already a class Name
            return $string;
        }

        $words = explode(' ', $string);

        $string = '';

        foreach ($words as $word) {
            $string .= ucfirst($word);
        }

        $string = $this->suffix($string, $suffix);

        return $string;
    }

    private function prefix(String $string, String $prefix)
    {
        if ($prefix != '')
            $string = ucfirst($prefix) . '\\' . $string;

        return $string;
    }

    private function suffix(String $string, String $suffix)
    {
        if ($suffix != '')
            $string = $string . ucfirst($suffix);

        return $string;
    }

    private function appendFeaturesPrefix(String $string)
    {
        return '\\App\\Features\\' . $string; // TODO publish config file containing
    }

    private function appendTasksPrefix(String $string)
    {
        return '\\App\\Tasks\\' . $string; // TODO publish config file containing
    }

    public function generate()
    {
        $output = '<?php';
        $output .= "\n";
        $output .= 'namespace App\\Features';

        if ($this->prefix)
            $output .= '\\' . ucfirst($this->prefix);

        $output .= ';';

        $output .= "    public function handle() {";

        foreach ($this->tasks as $task) {
            $output .= "\t";

            if ($task['return'])
                $output .= '$' . $task['return'] . ' = ';

            $output .= '$this->run(' . $task['class'];

            if ($task['expect'])
                $output .= ', $'.$task['expect'];

            $output .= ')';
        }

        $output .= "\n";
        $output .= "}";

        return $output;
    }
}
