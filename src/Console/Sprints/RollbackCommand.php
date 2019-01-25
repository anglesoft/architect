<?php

namespace Angle\Architect\Console\Sprints;

use Angle\Architect\Code\Compiler;
use Angle\Architect\Console\Command;
use Angle\Architect\Database\SprintRepository as Repository;
use Angle\Architect\Sprint;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @todo Add option to disable tests
     * @var string
     */
    protected $signature = 'sprint:rollback
        {--pretend : Simulates the operation and shows the list of would-be deleted files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last sprint';

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

        Compiler::revert();

        if ($this->option('pretend') == true) {
            Compiler::pretend();
        }

        $sprints = $this->repository->getSprintsToRollback();

        if ($sprints->count() == 0) {
            $this->info('Nothing to rollback.');
            exit;
        }

        $deletions = 0;

        $files = $this->sprint->getSprintFiles();

        $sprints = collect($files)->reject(function ($file) use ($sprints) {
            return ! $sprints->contains(function ($row) use ($file) {
                return $row->sprint == $this->sprint->getFileName($file);
            });
        });

        foreach ($sprints as $file) {
            $sprint = $this->sprint->resolve($file);

            Compiler::reset();

            try {
                $sprint->run(); // Invokes the compiler
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                exit;
            }

            if ($this->option('pretend') == false) {
                $this->repository->deleteByName(
                    $this->sprint->getFileName($file)
                );
            }
        }

        foreach (Compiler::stack() as $file) {
            if ($this->option('pretend') == false) {
                $this->line("<info>Deleted:</info> {$file}");
            } else {
                $this->line("<fg=cyan;bg=black>Would delete:</> {$file}");
            }
        }

        if ($deletions == 0) {
            $this->info('Nothing to rollback.');
        }
    }
}
