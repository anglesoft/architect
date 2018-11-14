<?php

namespace Angle\Architect\Code\Blueprints;

use Angle\Architect\Code\Blueprint;
use Closure;
use Illuminate\Support\Str;

class Sprint extends Blueprint
{
    protected $stub = 'sprint.stub';

    public function __construct(String $name, Closure $callback = null, String $prefix = '', String $suffix = '')
    {
        parent::__construct($name, $callback, $prefix, $suffix);

        $this->file = 'sprints/' . $this->getDatePrefix() . '_' . Str::snake($name) . '.php';
        $this->path = base_path($this->getFileName());
    }

    /**
     * Get the date prefix for the sprint.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
