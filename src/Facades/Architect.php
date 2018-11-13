<?php

namespace Angle\Architect\Facades;

use Angle\Architect\Generator\Blueprint;

use Closure;

class Architect
{
    static function describe($feature, Closure $callback = null, $prefix = '')
    {
        return new Blueprint($feature, $callback, $prefix);
    }

    static function feature($feature, Closure $callback = null, $prefix = '')
    {
        return new Blueprint($feature, $callback, $prefix);
    }
}
