<?php

namespace Angle\Architect\Code;

use Closure;
use Illuminate\Support\Str;

class Blueprint
{
    protected $stub = 'class.stub';
    protected $prefix = ''; // \Directory\ClassName
    protected $suffix = ''; // ClassNameSuffix
    protected $file = '';
    protected $path = '';
    protected $description = '';
    protected $name = '';
    protected $namespace = '';
    protected $methods = [];
    protected $method = []; // Current method
    protected $instructions = []; // Stores all instructions
    protected $instruction = []; // Stores only the current instruction

    public function __construct(String $description, Closure $callback = null, String $prefix = '', String $suffix = '')
    {
        $this->description = trim($description);
        $this->prefix = $this->makeClassNameFromString($prefix);
        $this->suffix = $this->makeClassNameFromString($suffix);
        $this->name = $this->makeClassNameFromString($this->description, '', $this->suffix);
        $this->namespace = $this->makeNamespaceFromString($this->prefix);
        $this->file = $this->makeFileName();
        $this->path = $this->makePath();

        if ( ! is_null($callback)) {
            $callback($this);
        }

        $this->pushPreviousInstruction();
    }

    public function __toString()
    {
        return $this->generate();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClassName()
    {
        return $this->getNamespace() . '\\' . $this->getName();
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getStub()
    {
        return $this->stub;
    }

    public function getFileName()
    {
        return $this->file;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getInstructions()
    {
        return $this->instructions;
    }

    public function getBlock() : String
    {
        return '//';
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getDescription() : String
    {
        return $this->description;
    }

    public function getUse() : String
    {
        return '';
    }

    public function getParameter() : String
    {
        return '';
    }

    public function classExists()
    {
        return class_exists($this->getName());
    }

    public function makeClassNameFromString(String $string, String $prefix = '', String $suffix = '')
    {
        if ( ! $this->isClassName($string)) { //  && ! is_null($string)
            $string = $this->makeStudlyString($string);
            $string = $this->addClassNameSuffix($string, $suffix);
        }

        return $string;
    }

    public function makeMethodNameFromString(String $string, String $prefix = '', String $suffix = '')
    {
        if ($prefix != '') {
            $prefix = Str::camel($prefix);
            $string = Str::studly($string);
        }

        if ($prefix != '');
            $suffix = Str::studly($suffix);

        return Str::camel($prefix . $string . $suffix);
    }

    public function makeNamespaceFromString(String $string)
    {
        $string = $this->makeClassNameFromString($string);
        $string = ltrim($string, '\\');
        $string = rtrim($string, '\\');

        return $string;
    }

    public function makeFileName(string $string = null) : string
    {
        if ($string != null) {
            $this->file = $string;
        }

        $file = $this->namespace . '\\' . $this->name . '.php';
        $file = str_replace('App\\', '', $file);
        $this->file = str_replace('\\', '/', $file);

        return $this->file;
    }

    public function makePath()
    {
        return $this->path = app_path($this->getFileName());
    }

    public function isClassName(String $string)
    {
        return str_contains('\\', $string);
    }

    public function isSentence(String $string)
    {
        return str_contains(' ', trim($string));
    }

    public function makeStudlyString(String $string)
    {
        $string = trim($string);

        if ( ! $this->isSentence($string)) {
            return Str::studly($string);
        }

        $words = explode(' ', $string);
        $string = '';

        foreach ($words as $word) {
            $string .= Str::studly($word);
        }

        return $string;
    }

    public function pushPreviousInstruction()
    {
        if ( ! empty($this->method) && ! empty($this->instruction))
            $this->methods[$this->method['name']]['instructions'][] = $this->instruction;

        if ( ! empty($this->instruction))
            $this->instructions[] = $this->instruction;
    }

    public function addClassNamePrefix(String $name, String $prefix = '') : String
    {
        if ($prefix != '')
            $name = Str::studly($prefix) . '\\' . $name;

        return $name;
    }

    public function addClassNameSuffix(String $name, String $suffix = '') : String
    {
        if ($suffix != '')
            $name = $name . Str::studly($suffix);

        return $name;
    }

    public function method(String $method, Array $options = []) : Blueprint
    {
        $name = $this->makeMethodNameFromString($method);
        $this->method['name'] = $name;

        foreach ($options as $key => $value)
            $this->method[$key] = $value;

        $this->methods[$name] = $this->method;

        return $this;
    }

    public function will(String $instruction) : Blueprint
    {
        $this->pushPreviousInstruction();

        $this->instruction['class'] = $this->makeClassNameFromString($instruction);

        return $this;
    }

    public function expect($parameter) // TODO expect array;
    {
        $this->instruction['expect'] = $parameter; // TODO makeVariableNameFromString

        return $this;
    }

    public function return($parameter) // TODO return list() multiple parameters
    {
        $this->instruction['return'] = $parameter; // TODO makeVariableNameFromString

        return $this;
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

        foreach ($this->instructions as $task) {
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
