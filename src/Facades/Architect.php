<?php

namespace Angle\Architect\Facades;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Blueprints\Feature;
use Angle\Architect\Code\Compiler;
use Angle\Architect\Database\SprintRepository as Repository;
use Closure;

class Architect
{
    /**
     * Enabling forcing allows to overwrite existing files.
     *
     * @var bool
     */
    public static $forcing = false;

    /**
     * Toggled to true while running sprints.
     *
     * @var bool
     */
    public static $sprinting = false;

    /**
     * Enabling pretending will show you the would-be created files.
     *
     * @var bool
     */
    public static $pretending = false;

    public static $stack = [];

    private static function hook(Blueprint $blueprint) : void
    {
        if (static::sprinting()) { // TODO || static::reverting()) { // running in console
            static::$stack[] = static::compile($blueprint, static::pretending(), static::forcing());
        }
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
     * Compiles a new blueprint.
     *
     * @param  \Angle\Architect\Code\Blueprint $blueprint
     * @param  bool $pretend
     * @param  bool $force
     * @return array
     */
    public static function compile(Blueprint $blueprint, bool $pretend = false, bool $force = false) : array
    {
        return (new Compiler($blueprint))->build($pretend, $force);
    }

    /**
     * Draws a new blueprint.
     *
     * @param  string $feature
     * @param  Closure $callback
     * @param  string $prefix
     * @return \Angle\Architect\Code\Blueprint
     */
    public static function describe(string $feature, Closure $callback = null, string $prefix = '') : Blueprint
    {
        return new Blueprint($feature, $callback, $prefix);
    }

    /**
     * Draws a new feature blueprint.
     *
     * @todo improve hook to compiler
     *
     * @param  string $feature
     * @param  Closure $callback
     * @param  string $prefix
     * @return \Angle\Architect\Code\Blueprint
     */
    public static function feature(string $feature, Closure $callback = null, string $prefix = '') : Blueprint
    {
        $blueprint = new Feature($feature, $callback, $prefix);

        static::hook($blueprint);

        return $blueprint;
    }

    /**
     * Checks if the package is installed.
     *
     * @return bool
     */
    public static function installed() : bool
    {
        return (new Repository)->repositoryExists();
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
    public static function stop() : void
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
     * @return bool [description]
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
}
