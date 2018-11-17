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
     * Create a new blueprint.
     *
     * @param  string $feature
     * @param  Closure $callback
     * @param  string $prefix
     * @return \Angle\Architect\Code\Blueprint
     */
    public static function describe(string $feature, Closure $callback = null, string $prefix = '') : Blueprint
    {
        return Compiler::observe(new Blueprint($feature, $callback, $prefix));
    }

    /**
     * Create a new feature blueprint.
     *
     * @todo improve hook to compiler
     *
     * @param  string $feature
     * @param  Closure $callback
     * @param  string $prefix
     * @return \Angle\Architect\Code\Blueprints\Feature
     */
    public static function feature(string $feature, Closure $callback = null, string $prefix = '') : Blueprint
    {
        return Compiler::observe(new Feature($feature, $callback, $prefix));
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
}
