<?php

namespace Angle\Architect\Console;

use Illuminate\Console\Command;
use Angle\Architect\Database\SprintRepository;

class ArchitectInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'architect:install {database? : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the sprint repository';

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
    public function __construct(SprintRepository $repository)
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
        if ( ! $this->repository->repositoryExists()) {
            $this->repository->setSource($this->argument('database'));
            $this->repository->createRepository();
            $this->info('Sprint table created successfully.');
        } else {
            $this->error('Sprint table already exists.');
        }
    }
}
