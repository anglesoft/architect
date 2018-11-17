<?php

namespace Angle\Architect\Code;

use Angle\Architect\Code\Stub;
use Angle\Architect\Code\Compass;
use Closure;
use Illuminate\Support\Str;

class Blueprint
{
    protected $name = '';
    protected $namespace = '';
    protected $blueprints = []; // A blueprint can hold other Blueprints
    protected $use = '';
    protected $uses = [];
    protected $stub = 'class.stub';
    protected $prefix = ''; // \Directory\ClassName
    protected $suffix = ''; // ClassNameSuffix
    protected $file = '';
    protected $path = '';
    protected $paths = [];
    protected $description = '';
    protected $methods = [];
    protected $method = []; // Current method
    protected $instructions = []; // Stores all instructions
    protected $instruction = []; // Stores only the current instruction

    public function __construct(string $description, Closure $callback = null, string $prefix = '', string $suffix = '')
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

    public function __toString() : string
    {
        return $this->getCode();
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getClassName() : string
    {
        return $this->getNamespace() . '\\' . $this->getName();
    }

    public function getNamespace() : string
    {
        return $this->namespace;
    }

    public function getStubFileName() : string
    {
        return $this->stub;
    }

    public function getFileName() : string
    {
        return $this->file;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function getPaths() : array // makePaths?
    {
        $this->paths[] = $this->path;

        $blueprints = $this->getBlueprints();

        if ( ! empty($blueprints)) {
            foreach ($blueprints as $key => $blueprint) {
                $this->paths[] = $blueprint->getPath();
            }
        }

        return $this->paths;
    }

    public function getUses() : array
    {
        return $this->uses;
    }

    public function getInstructions() : array
    {
        return $this->instructions;
    }

    public function getBlock() : string
    {
        return '//';
    }

    public function getMethods() : array
    {
        return $this->methods;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getUse() : string
    {
        if (count($this->uses) == 0)
            return '';

        $code = '';

        foreach ($this->uses as $use) {
            $code .= "
use {$use};";
        }

        return $code;
    }

    public function getParameter() : string
    {
        return '';
    }

    public function getBlueprints() : array
    {
        // Ensure all blueprints are properly built
        // if ( ! $this->hasBlueprints())
        //     $this->compose();

        return $this->blueprints;
    }

    public function getCode() : string
    {
        return $this->code = (new Stub($this))->getCode();
    }

    public function classExists() : bool
    {
        return class_exists($this->getName());
    }

    protected function makeClassNameFromString(string $string, string $prefix = '', string $suffix = '') : string
    {
        if ( ! $this->isClassName($string)) { //  && ! is_null($string)
            $string = $this->makeStudlyString($string);
            $string = $this->addClassNameSuffix($string, $suffix);
        }

        return $string;
    }

    protected function makeMethodNameFromString(string $string, string $prefix = '', string $suffix = '') : string
    {
        if ($prefix != '') {
            $prefix = Str::camel($prefix);
            $string = Str::studly($string);
        }

        if ($prefix != '');
            $suffix = Str::studly($suffix);

        return Str::camel($prefix . $string . $suffix);
    }

    protected function makeNamespaceFromString(string $string) : string
    {
        $string = $this->makeClassNameFromString($string);
        $string = ltrim($string, '\\');
        $string = rtrim($string, '\\');

        return $string;
    }

    protected function sanitizeString(string $string)
    {
        return preg_replace('/\W+/', ' ', $string);
    }

    protected function makePropertyNameFromString(string $string) : string
    {
        $case = config('architect.compiler.properties');
        $string = $this->sanitizeString($string);

        if ($case == 'camel') {
            $string = Str::camel($string);
        } else {
            $string = Str::snake($string);
        }

        return $string; //preg_replace('/\W+/', '', $string);
    }

    protected function makeFileName(string $string = null) : string
    {
        if ($string != null) {
            $this->file = $string;
        }

        $file = $this->namespace . '\\' . $this->name . '.php';
        $file = str_replace('App\\', '', $file);
        $this->file = str_replace('\\', '/', $file);

        return $this->file;
    }

    protected function makePath() : string
    {
        return $this->path = app_path($this->getFileName());
    }

    protected function isClassName(string $string) : bool
    {
        return str_contains('\\', $string) && $string != '';
    }

    protected function isSentence(string $string) : bool
    {
        return str_contains(' ', trim($string));
    }

    protected function isBlueprint($suspect) : bool
    {
        return is_a($suspect, Blueprint::class);
    }

    protected function makeStudlyString(string $string) : string
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

    protected function pushPreviousInstruction() : void
    {
        if ( ! empty($this->method) && ! empty($this->instruction))
            $this->methods[$this->method['name']]['instructions'][] = $this->instruction;

        if ( ! empty($this->instruction))
            $this->instructions[] = $this->instruction;

        $this->clearCache();
    }

    protected function clearCache() : void
    {
        $this->instruction = [];
        $this->method = [];
    }

    protected function pushUse($class) : void
    {
        $this->uses[] = $class;
    }

    protected function addClassNamePrefix(string $name, string $prefix = '') : string
    {
        if ($prefix != '')
            $name = Str::studly($prefix) . '\\' . $name;

        return $name;
    }

    protected function addClassNameSuffix(string $name, string $suffix = '') : string
    {
        if ($suffix != '')
            $name = $name . Str::studly($suffix);

        return $name;
    }

    /**
     * Add blueprint to array.
     *
     * @param  Blueprint $blueprint
     * @return Blueprint
     */
    protected function pushBlueprint(Blueprint $blueprint) : Blueprint
    {
        return $this->blueprints[] = $blueprint;
    }

    /**
     * Verifies if there are any sub blueprints.
     *
     * @return bool
     */
    public function hasBlueprints() : bool
    {
        return count($this->blueprints) > 0;
    }

    /**
     * Add blueprint to blueprint.
     *
     * @param  Blueprint $blueprint
     * @return Blueprint
     */
    public function blueprint(Blueprint $blueprint) : Blueprint
    {
        $this->pushBlueprint($blueprint);

        return $this;
    }

    /**
     * Add a new method to the class.
     *
     * @param  string    $string
     * @param  array     $options
     * @return Blueprint
     */
    public function method(string $string, Array $options = []) : Blueprint
    {
        $this->pushPreviousInstruction();

        $name = $this->makeMethodNameFromString($string);

        $this->method['name'] = $name;

        foreach ($options as $key => $value)
            $this->method[$key] = $value;

        $this->methods[$name] = $this->method;

        return $this;
    }

    /**
     * Use class.
     *
     * @param  string|Blueprint $class
     * @return Blueprint
     */
    public function use($class) : Blueprint
    {
        if ($this->isBlueprint($class)) {
            $this->pushUse($class->getClassName());
        }

        if ($this->isClassName($class)) {
            $this->pusUse($class);
        }

        return $this;
    }

    /**
     * Add instruction.
     *
     * @param  string    $instruction
     * @return Blueprint
     */
    public function will(string $instruction) : Blueprint
    {
        $this->pushPreviousInstruction();

        $this->instruction['class'] = $this->makeClassNameFromString($instruction);

        return $this;
    }

    /**
     * Add parameter.
     *
     * @todo   Let $parameter to be an array, so we can pass multiple parameters.
     * @todo   Sanitize parameter with a method like makeVariableNameFromString
     *
     * @param  string $parameter
     * @return Blueprint
     */
    public function expect($parameter)
    {
        $this->instruction['expect'] = $this->makePropertyNameFromString($parameter);

        return $this;
    }

    /**
     * Add return parameter.
     *
     * @param  string $parameter
     * @return Blueprint
     */
    public function return($parameter)
    {
        $this->instruction['return'] = $this->makePropertyNameFromString($parameter);

        return $this;
    }

    /**
     * Pre-compiler hook.
     *
     * @return void
     */
    public function compose() : Blueprint
    {
        $this->getPaths();

        return $this;
    }
}
