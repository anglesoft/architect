<?php

namespace Angle\Architect\Facades;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Blueprints\Feature;
use Angle\Architect\Database\SprintRepository;
use Closure;

class Architect
{
    public static $sprint = false;
    public static $pretend = false;
    public static $force = false;

    static function describe($feature, Closure $callback = null, $prefix = '')
    {
        return new Blueprint($feature, $callback, $prefix);
    }

    static function feature($feature, Closure $callback = null, $prefix = '')
    {
        return new Feature($feature, $callback, $prefix);
    }

    static function sprint()
    {
        static::$sprint = true;
    }

    static function stop()
    {
        static::$sprint = false;
    }

    static function installed()
    {
        return with(new SprintRepository)->repositoryExists();
    }

    static function isSprinting()
    {
        return static::$sprint === true;
    }

    static function pretend()
    {
        static::$pretend = true;
    }

    static function isPretending()
    {
        return static::$pretend === true;
    }

    static function force()
    {
        static::$force = true;
    }

    static function isForcing()
    {
        return static::$force === true;
    }
}
