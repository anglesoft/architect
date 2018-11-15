<?php

namespace Angle\Architect\Console;

use Angle\Architect\Facades\Architect;
use Angle\Architect\Sprint;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SprintCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sprint {--pretend : Simulates the operation and displays the list of would-be created files}
                {--force : Force overwritting of existing files}'; // TODO add option to generate tests or not

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
     * Create a new sprint command instance.
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
     * @return mixed
     */
    public function handle() : void
    {
        Architect::sprint();

        if ($this->option('pretend') === true)
            Architect::pretend();

        if ($this->option('force') === true)
            Architect::force();

        dump([
            'sprint' => $this->sprint,
            'pretend' => Architect::isPretending(),
            'force' => Architect::isForcing(),
        ]);

        // Find file(s) to run
        // Compile those files
        // Save batch to db
        // Output result
    }
}
