<?php

namespace Angle\Architect\Code;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Stub;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class Builder
{
    protected $blueprint;
    protected $files;

    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->files = app('files');
        $this->stub = new Stub($blueprint);
    }

    public function save(bool $pretend = false, bool $force = false) : string
    {
        $name = $this->blueprint->getClassName();

        if ( ! $force)
            $this->ensureClassDoesntAlreadyExist($name);

        $path = $this->blueprint->getPath();
        $code = $this->stub->getCode();

        if ( ! $pretend)
            $this->files->put($path, $code);

        return $this->blueprint->getFileName();
    }

    public function delete()
    {
        //
    }

    /**
     * Ensure that a class with the given name doesn't already exist.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureClassDoesntAlreadyExist($name) : void
    {
        if (class_exists($name)) {
            throw new InvalidArgumentException("Class {$name} already exists.");
        }
    }
}
