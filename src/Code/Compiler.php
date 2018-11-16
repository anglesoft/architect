<?php

namespace Angle\Architect\Code;

use Angle\Architect\Code\Blueprint;
use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class Compiler
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


    /**
     * Storage for created|deleted file references.
     * @var array
     */
    protected $stack = [];

    /**
     * Create a new compiler instance.
     *
     * @param Blueprint $blueprint [description]
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->files = app('files');

        $this->blueprint->compose(); // Pre-compiler hook
    }

    /**
     * Write blueprint code to disk.
     *
     * @param  boolean $pretend
     * @param  boolean $force
     * @return array|void
     * @throws \InvalidArgumentException
     */
    public function build(bool $pretend = false, bool $force = false) : array
    {
        $blueprints = $this->make();

        foreach ($blueprints as $blueprint) {
            $name = $blueprint->getClassName();

            if ( ! $force && $blueprint->classExists())
                throw new InvalidArgumentException("Class {$name} already exists.");

            $path = $blueprint->getPath();
            $code = $blueprint->getCode();

            if ( ! $pretend)
                $this->write($path, $code);

            $this->stack[] = $blueprint->getFileName();
        }

        return $this->stack;
    }

    /**
     * Builds an array representation of all blueprints
     *
     * @return array
     */
    public function make()
    {
        $this->blueprints[] = $this->blueprint;

        if ($this->blueprint->hasBlueprints()) {
            foreach ($this->blueprint->getBlueprints() as $blueprint) {
                $this->blueprints[] = $blueprint;
            }
        }

        return $this->blueprints;
    }

    public function write($path, $code) : void
    {
        $this->files->put($path, $code);
    }

    public function destroy()
    {
        // $paths = $this->blueprint->getFiles();
        // $this->files->delete($paths);
    }
}
