<?php

namespace Angle\Architect\Code;

use Angle\Architect\Code\Blueprint;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class Stub
{
    protected $blueprint;
    protected $path;
    protected $files;
    protected $content;
    protected $parts;
    protected $code = '';

    /**
     * Stub constructor
     *
     * @return void
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->path = __DIR__ . '/stubs/' . $this->blueprint->getStub();
        $this->files = app('files');
        $this->content = $this->files->get($this->path);
        $this->parts = $this->parse();
        $this->code = $this->compile();
    }

    /**
     * Replace parts
     *
     * @return string
     */
    public function replace($key, $value, $code) : string
    {
        return str_replace('{{'. $key .'}}', $value, $code);
    }

    /**
     * Parse the stub to find parts needing replacement.
     *
     * @return array
     */
    public function parse() : array
    {
        $count = preg_match_all('/{{((?:[^}]|}[^}])+)}}/', $this->content, $matches);

        if ($count == 0)
            throw new InvalidArgumentException("Stub file [{$this->path}] doesn't have any part to replace.");

        return $matches[1];
    }

    /**
     * Compiles the stub and returns the generated code.
     *
     * @return string
     */
    public function compile() : string
    {
        $this->code = $this->content;

        foreach ($this->parts as $key) {
            $getter = 'get' . ucfirst($key);
            $value = $this->blueprint->{$getter}();
            $this->code = $this->replace($key, $value, $this->code);
        }

        return $this->code;
    }

    /**
     * Get the path to the stub.
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * The filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles() : Filesystem
    {
        return $this->files;
    }

    /**
     * Get the blueprint used by the stub.
     *
     * @return \Angle\Architect\Code\Blueprint
     */
    public function getBlueprint() : Blueprint
    {
        return $this->blueprint;
    }

    /**
     * Get the content to the stub.
     *
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * Get the generated code.
     *
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * Return parts that need to be replaced.
     *
     * @return array
     */
    public function getParts() : array
    {
        return $this->parts;
    }
}
