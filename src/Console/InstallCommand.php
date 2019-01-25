<?php

namespace Angle\Architect\Console;

use Angle\Architect\Database\SprintRepository as Repository;
use Angle\Architect\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'architect:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Architect package installer';

    /**
     * The repository instance.
     *
     * @var \Angle\Architect\Database\SprintRepository
     */
    protected $repository;

    /**
     * Create a new architect install command instance.
     *
     * @param  \Angle\Architect\Database\SprintRepository  $repository
     * @return void
     */
    public function __construct(Repository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->installed() && ! $this->confirm('Architect is already installed. Continue anyway?'))
            exit;

        // Create configuration file.
        $config = file_get_contents(__DIR__.'/../../config/architect.php');

        $connection = $this->ask('Which database connection will store the sprints?', 'mysql');
        $config = str_replace("'connection' => env('DB_CONNECTION', 'mysql')", "'connection' => env('DB_CONNECTION', '{$connection}')", $config);

        $table = $this->ask('Which database table will store the sprints?', 'sprints');
        $config = str_replace("'table' => 'sprints'", "'table' => '{$table}'", $config);

        $path = $this->ask('Where should we store sprint files?', 'sprints');
        $config = str_replace("'path' => 'sprints'", "'path' => '{$path}'", $config);
        $this->makeDirectoryFromPath($path);

        $namespace = $this->ask('What will be the features namespace?', 'App\Features');
        $config = str_replace("'features' => 'App\Features'", "'features' => '{$namespace}'", $config);
        $this->makeDirectoryFromNamespace($namespace);

        $namespace = $this->ask('What will be the tasks namespace?', 'App\Tasks');
        $config = str_replace("'tasks' => 'App\Tasks'", "'tasks' => '{$namespace}'", $config);
        $this->makeDirectoryFromNamespace($namespace);

        $case = $this->ask('Which case to use for class properties? (camel|snake)', 'camel');
        $config = str_replace("'properties' => 'snake'", "'properties' => '{$case}'", $config);

        $file = base_path('config/architect.php');

        file_put_contents($file, $config);

        $this->line('<comment>Created config:</comment>   config/architect.php');

        $this->callSilent('config:clear');
        $this->callSilent('config:cache');

        $this->repository->setTable($table);
        $this->repository->setSource($connection);

        if ( ! $this->repository->repositoryExists()) {
            $this->repository->createRepository();
            $this->line("<comment>Created table:</comment>  {$table}");
        }
    }

    private function makeDirectoryFromPath(string $path) : bool
    {
        if ( ! app('files')->exists($path)) {
            return app('files')->makeDirectory($path, 0777, true);
        }

        $this->line("<comment>Directory already exists:</comment>   {$path}");

        return false;
    }

    private function makeDirectoryFromNamespace(string $namespace) : bool
    {
        return $this->makeDirectoryFromPath(
            str_replace('\\', '/', lcfirst($namespace))
        );
    }
}
