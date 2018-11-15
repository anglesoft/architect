<?php

namespace Angle\Architect;

use Illuminate\Support\Collection;

class Sprint
{
    public function run()
    {

    }

    public function revert()
    {
        // get run code and delete all files created
    }

    /**
     * Get all of the sprint files in a given path.
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getSprintFiles($paths) : array
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return app('files')->glob($path . '/*_*.php');
        })->all();
    }

    /**
     * Require in all the sprint files in a given path.
     *
     * @param  array   $files
     * @return void
     */
    public function requireFiles(array $files) : void
    {
        foreach ($files as $file) {
            app('files')->requireOnce($file);
        }
    }

    /**
     * Require the existing sprint files.
     *
     * @param  array   $files
     * @return void
     */
    public function requireSprintFiles() : void
    {
        $files = $this->getSprintFiles('sprints');
        $this->requireFiles($files);
    }
}
