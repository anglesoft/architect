<?php

namespace Angle\Architect\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class MakeSprintCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:sprint {name? : The name of the sprint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sprint file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name'); // TODO sanitize argument (alpha-num)

        $blueprint = new \Angle\Architect\Code\Blueprints\Sprint($name);
        $builder = new \Angle\Architect\Code\Builder($blueprint);

        $sprints = $this->getSprintFiles('sprints');

        $this->requireFiles($sprints);

        if ($blueprint->classExists()) {
            throw new \ErrorException("Class already exists [{$blueprint->getName()}]");
        }

        $path = $builder->save();

        $this->info("Sprint file [$path] sucessfully created.");
    }

    /**
     * Get all of the sprint files in a given path.
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getSprintFiles($paths)
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
    public function requireFiles(array $files)
    {
        foreach ($files as $file) {
            app('files')->requireOnce($file);
        }
    }
}
