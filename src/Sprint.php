<?php

namespace Angle\Architect;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Sprint
{
    /**
     * Run the sprint.
     *
     * @return void
     */
    public function run()
    {
        //
    }

    /**
     * Revert the sprint.
     *
     * @return void
     */
    public function revert()
    {
        //
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
    public function requireSprintFiles(string $path) : void
    {
        $files = $this->getSprintFiles($path);
        $this->requireFiles($files);
    }

    /**
     * Requires sprint file.
     *
     * @param string $file
     */
    public function requireSprint(string $file) : void
    {
        app('files')->requireOnce($file);
    }


    /**
     * Resolve a sprint instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file) : object
    {
        $this->requireSprint($file);

        $class = $this->getClassName($file);

        return new $class;
    }

    /**
     * Returns class name from file.
     *
     * @param  string $file
     * @return string
     */
    public function getClassName(string $file) : string
    {
        return str_replace('.php', '', Str::studly(implode('_', array_slice(explode('_', $file), 4)))) . 'Sprint';
    }

    /**
     * Returns file name without exthension.
     *
     * @param  string $file
     * @return string
     */
    public function getFileName(string $file, string $path) : string
    {
        return str_replace('.php', '', str_replace($path . '/', '', $file));
    }

    /**
     * Get sprint path from name.
     *
     * @param  string $sprint
     * @param  string $path
     * @return string
     */
    public function getPath(string $sprint, string $path) : string
    {
        return $path . '/' . $sprint . '.php';
    }
}
