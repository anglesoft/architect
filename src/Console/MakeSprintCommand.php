<?php

namespace Angle\Architect\Console;

use Angle\Architect\Sprint;
use Angle\Architect\Code\Blueprints\Sprint as Blueprint;
use Angle\Architect\Code\Builder;
use Illuminate\Console\Command;

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
     * The sprint instance.
     *
     * @var \Angle\Architect\Sprint
     */
    protected $sprint;

    /**
     * Create a new command instance.
     *
     * @param  \Angle\Architect\Sprint  $sprint
     * @return void
     */
    public function __construct(Sprint $sprint)
    {
        parent::__construct();

        $this->sprint = $sprint;
    }

    /**
     * Execute the console command.
     *
     * @todo   sanitize argument (alpha-num)
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $blueprint = new Blueprint($name);
        $builder = new Builder($blueprint);

        // Sprint classes being out of scope,
        // we need to require them manually
        $this->sprint->requireSprintFiles();

        if ($blueprint->classExists()) {
            return $this->line("<error>Class already exists:</error> {$blueprint->getName()}");
        }

        $file = $builder->save();

        return $this->line("<info>Created Sprint:</info> {$file}");
    }
}
