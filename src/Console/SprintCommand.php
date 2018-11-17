<?php

namespace Angle\Architect\Console;

use Angle\Architect\Code\Compiler;
use Angle\Architect\Database\SprintRepository as Repository;
use Angle\Architect\Sprint;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SprintCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @todo Add option to disable tests
     * @var string
     */
    protected $signature = 'sprint {--pretend : Simulates the operation and displays the list of would-be created files}
                {--force : Force overwritting of existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run pending sprints';

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
     * Create a new sprint command instance.
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
     * @return mixed
     */
    public function handle() : void
    {
        Compiler::sprint();

        if ($this->option('pretend') === true)
            Compiler::pretend();

        if ($this->option('force') === true)
            Compiler::force();

        $files = $this->sprint->getSprintFiles('sprints');

        if (count($files) == 0) {
            $this->info('Nothing to run.');
            exit;
        }

        $runs = 0;

        foreach ($files as $file) {
            $name = $this->sprint->getFileName($file, $path = config('architect.sprints.path'));

            if ($this->repository->hasRun($name)) {
                continue;
            }

            if ($this->option('pretend') == false) {
                $this->line("<comment>Running:</comment> {$name}");
            } else {
                $this->line("<fg=magenta;bg=black>Pretending:</> {$name}");
            }

            $path = $this->sprint->getPath($name, $path);
            $sprint = $this->sprint->resolve($path);

            Compiler::reset();

            // This will invoke the compiler implicitly,
            // as all blueprints will be registered.
            $sprint->run();

            // $this->repository->create($name);
            $runs++;

            foreach (Compiler::stack() as $file) {
                if ($this->option('pretend') == false) {
                    $this->line("<info>Created:</info> {$file}");
                } else {
                    $this->line("<fg=cyan;bg=black>Would create:</> {$file}");
                }
            }
        }

        if ($runs == 0) {
            $this->info('Nothing to run.');
        }
    }
}
