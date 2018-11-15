<?php

namespace Angle\Architect\Code;

use InvalidArgumentException;
use Angle\Architect\Code\Blueprint;
use Illuminate\Filesystem\Filesystem;

class Builder
{
    /**
     * Blueprint instance.
     *
     * @var \Angle\Architect\Code\Blueprint
     */
    protected $blueprint;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->files = app('files');
    }

    /**
     * Ensure that a class with the given name doesn't already exist.
     *
     * @return string|void
     *
     * @throws \InvalidArgumentException
     */
    public function save(bool $pretend = false, bool $force = false) : string
    {
        $name = $this->blueprint->getClassName();

        if ( ! $force && $this->blueprint->classExists())
            throw new InvalidArgumentException("Class {$name} already exists.");

        $path = $this->blueprint->getPath();
        $code = $this->blueprint->getCode();

        if ( ! $pretend)
            $this->files->put($path, $code);

        return pathinfo($this->blueprint->getFileName(), PATHINFO_FILENAME);
    }

    public function delete()
    {
        // $paths = $this->blueprint->getFiles();
        // $this->files->delete($paths);
    }
}
