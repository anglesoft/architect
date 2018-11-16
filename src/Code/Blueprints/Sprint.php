<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Closure;
use Illuminate\Support\Str;

class Sprint extends Blueprint
{
    /**
     * Reference to the stub file.
     *
     * @var string
     */
    protected $stub = 'sprint.stub';

    /**
     * Create a new sprint blueprint instance.
     *
     * @todo Allow to configure pre/suffixes
     * @param string $name
     * @param Closure $callback
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(string $name, Closure $callback = null, string $prefix = '', string $suffix = '')
    {
        parent::__construct($name, $callback, $prefix, $suffix = 'Sprint');

        $this->file = 'sprints/' . $this->getDatePrefix() . '_' . Str::snake($name) . '.php';
        $this->path = base_path($this->getFileName());
    }

    /**
     * Get the date prefix for the sprint.
     *
     * @return string
     */
    protected function getDatePrefix() : string
    {
        return date('Y_m_d_His');
    }
}
