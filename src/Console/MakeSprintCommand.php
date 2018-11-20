<?php

namespace Angle\Architect\Console;

use Angle\Architect\Code\Blueprints\Sprint as Blueprint;
use Angle\Architect\Code\Compiler;
use Angle\Architect\Console\Command;
use Angle\Architect\Database\SprintRepository as Repository;
use Angle\Architect\Sprint;

class MakeSprintCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:sprint {name : The name of the sprint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sprint file';

    /**
     * The sprint instance.
     *
     * @var \Angle\Architect\Sprint
     */
    protected $sprint;

    /**
     * The repository instance.
     *
     * @var \Angle\Architect\Database\SprintRepository
     */
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @param  \Angle\Architect\Sprint  $sprint
     * @param  \Angle\Architect\Database\SprintRepository  $repository
     * @return void
     */
    public function __construct(Sprint $sprint, Repository $repository)
    {
        parent::__construct();

        $this->sprint = $sprint;
        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @todo   sanitize argument (alpha-num)
     * @return mixed
     */
    public function handle()
    {
        $this->ensureIsInstalled();

        $name = $this->argument('name');

        $blueprint = new Blueprint($name);
        $compiler = new Compiler($blueprint);

        // Sprint classes being out of scope,
        // we need to require them manually
        $this->sprint->requireSprintFiles(config('architect.sprints.path'));

        if ($blueprint->classExists()) {
            return $this->line("<error>Class already exists:</error> {$blueprint->getName()}");
        }

        $stack = $compiler->build();
        $file = pathinfo(end($stack), PATHINFO_FILENAME);

        return $this->line("<info>Created Sprint:</info> {$file}");
    }
}
