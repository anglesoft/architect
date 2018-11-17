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
    private $blueprint;

    /**
     * Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;


    /**
     * Storage for created|deleted file references.
     * @var array
     */
    private static $stack = [];

    /**
     * Enabling forcing allows to overwrite existing files.
     *
     * @var bool
     */
    private static $forcing = false;

    /**
     * Toggled to true while running sprints.
     *
     * @var bool
     */
    private static $sprinting = false;

    /**
     * Enabling pretending will show you the would-be created files.
     *
     * @var bool
     */
    private static $pretending = false;

    /**
     * Create a new compiler instance.
     *
     * @param Blueprint $blueprint [description]
     */
    public function __construct(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        $this->files = app('files');
    }

    /**
     * Returns the stack array containing file references.
     *
     * @return array
     */
    public static function stack() : array
    {
        return static::$stack;
    }

    /**
     * Sets sprinting to true.
     *
     * @return void
     */
    public static function sprint() : void
    {
        static::$sprinting = true;
    }

    /**
     * Sets sprinting to false.
     *
     * @return void
     */
    public static function resume() : void
    {
        static::$sprinting = false;
    }

    /**
     * Checks if the app is currently running sprint(s).
     *
     * @return boolean
     */
    public static function sprinting() : bool
    {
        return static::$sprinting === true;
    }

    /**
     * Sets pretending as true.
     *
     * @return void
     */
    public static function pretend() : void
    {
        static::$pretending = true;
    }

    /**
     * Checks if sprint is currently pretending to run.
     * It won't write any file while pretending is on.
     *
     * @return bool
     */
    public static function pretending() : bool
    {
        return static::$pretending === true;
    }

    /**
     * Sets forcing to true.
     *
     * @return void
     */
    public static function force() : void
    {
        static::$forcing = true;
    }

    /**
     * Checks if forces writing of files.
     *
     * @return bool
     */
    public static function forcing() : bool
    {
        return static::$forcing === true;
    }

    /**
     * Compiles a new blueprint.
     *
     * @param  \Angle\Architect\Code\Blueprint $blueprint
     * @param  bool $pretend
     * @param  bool $force
     * @return array
     */
    public static function compile(Blueprint $blueprint, bool $pretend = false, bool $force = false) : array
    {
        return (new self($blueprint))->build($pretend, $force);
    }

    /**
     * Register blueprint for compilation.
     *
     * @param Blueprint $blueprint
     * @return Blueprint
     */
    public static function observe(Blueprint $blueprint) : Blueprint
    {
        if (static::sprinting()) { // TODO || static::reverting()) { // running in console
            static::$stack = static::compile($blueprint, static::pretending(), static::forcing());
        }

        return $blueprint;
    }

    /**
     * Builds an array representation of all blueprints
     *
     * @return array
     */
    public function make()
    {
        $this->blueprint->compose(); // Pre-compile sub-blueprints

        $this->blueprints[] = $this->blueprint;

        if ($this->blueprint->hasBlueprints()) {
            foreach ($this->blueprint->getBlueprints() as $blueprint) {
                $this->blueprints[] = $blueprint;
            }
        }

        return $this->blueprints;
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

            static::$stack[] = $blueprint->getFileName();
        }

        return static::$stack;
    }

    /**
     * Write file to disk.
     *
     * @param $path
     * @param $code
     */
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
