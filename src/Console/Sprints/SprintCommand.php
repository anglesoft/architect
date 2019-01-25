<?php

namespace Angle\Architect\Console\Sprints;

use Angle\Architect\Code\Compiler;
use Angle\Architect\Console\Command;
use Angle\Architect\Database\SprintRepository as Repository;
use Angle\Architect\Sprint;

class SprintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @todo Add option to disable tests
     * @var string
     */
    protected $signature = 'sprint
        {--pretend : Simulates the operation}
        {--force : Force overwriting of existing files}';

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
        $this->ensureIsInstalled();

        Compiler::sprint();

        if ($this->option('pretend') == true) {
            Compiler::pretend();
        }

        if ($this->option('force') == true) {
            Compiler::force();
        }

        $files = $this->sprint->getSprintFiles();

        if (count($files) == 0) {
            $this->info('Nothing to run.');
            exit;
        }

        $runs = 0;
        $batch = $this->repository->getNextBatchNumber();

        foreach ($files as $file) {
            $name = $this->sprint->getFileName($file);

            if ($this->repository->hasRun($name)) {
                continue;
            }

            if ($this->option('pretend') == false) {
                $this->line("<comment>Running:</comment> {$name}");
            } else {
                $this->line("<fg=magenta;bg=black>Pretending:</> {$name}");
            }

            $path = $this->sprint->getPath($name);
            $sprint = $this->sprint->resolve($path);

            Compiler::reset();

            // try {
                $sprint->run(); // Invokes the compiler
            // } catch (\Exception $e) {
            //     $this->error($e->getMessage());
            //     exit;
            // }

            if ($this->option('pretend') == false) {
                $this->repository->create($name, $batch);
            }

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
