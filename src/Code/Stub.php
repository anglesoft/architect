<?php

namespace Angle\Architect\Code;

use Angle\Architect\Code\Blueprint;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class Stub
{
    /**
     * Blueprint instance.
     *
     * @var \Angle\Architect\Code\Blueprint
     */
    protected $blueprint;

    /**
     * Path to the stub file.
     *
     * @var string
     */
    protected $path;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Raw stub file content
     * @var string
     */
    protected $content;

    /**
     * Parts extracted from the stub.
     * @var array
     */
    protected $parts = [];

    /**
     * Generated code.
     *
     * @var string
     */
    protected $code = '';

    /**
     * Create a new stub instance.
     *
     * @param Blueprint $blueprint
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->path = __DIR__ . '/stubs/' . $this->blueprint->getStubFileName();
        $this->files = app('files');
        $this->content = $this->files->get($this->path);
        $this->parts = $this->parse();
        $this->code = $this->compile();
    }

    /**
     * The path to the to-be-generated file.
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

    /**
     * Replace parts from the stub file.
     *
     * @param  string $key
     * @param  string $value
     * @param  string $code
     * @return string
     */
    public function replace(string $key, string $value, string $code) : string
    {
        return str_replace('{{'. $key .'}}', $value, $code);
    }

    /**
     * Parse the stub to find {{parts}} needing replacement.
     *
     * @return array
     */
    public function parse() : array
    {
        preg_match_all('/{{((?:[^}]|}[^}])+)}}/', $this->content, $matches);

        return $matches[1];
    }

    /**
     * Replace parts from content and return generated code.
     * Each stub file can have its own parts to replace as
     * long as a matching getter on the blueprint exists.
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
}
