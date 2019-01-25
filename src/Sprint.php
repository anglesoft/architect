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

    private function getSprintsPath()
    {
        return config('architect.sprints.path');
    }

    /**
     * Get all of the sprint files in a given path.
     *
     * @return array
     */
    public function getSprintFiles() : array
    {
        return Collection::make($this->getSprintsPath())->flatMap(function ($path) {
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
        $this->requireFiles(
            $this->getSprintFiles()
        );
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
    public function resolve(string $file) : object
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
    public function getFileName(string $file) : string
    {
        return str_replace('.php', '', str_replace($this->getSprintsPath() . '/', '', $file));
    }

    /**
     * Get sprint path from name.
     *
     * @param  string $sprint
     * @param  string $path
     * @return string
     */
    public function getPath(string $sprint, string $path = null) : string
    {
        $path = $path ?? config('architect.sprints.path');

        return $path . '/' . $sprint . '.php';
    }
}
