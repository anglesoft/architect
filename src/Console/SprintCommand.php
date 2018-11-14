<?php

namespace Angle\Architect\Console;

use Angle\Architect\Facades\Architect;
use Angle\Architect\Sprint\Runner;
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
                {--force : Force overwritting of existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs pending sprints.';

    /**
     * The runner instance.
     *
     * @var \Angle\Architect\Sprint\Runner
     */
    protected $runner;

    /**
     * Create a new sprint command instance.
     *
     * @param  \Angle\Architect\Sprint\Runner  $runner
     * @return void
     */
    public function __construct(Runner $runner)
    {
        parent::__construct();

        $this->runner = $runner;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Architect::sprint();

        if ($this->option('pretend') === true)
            Architect::pretend();

        if ($this->option('force') === true)
            Architect::force();

        dump([
            'sprint' => $this->runner,
            'pretend' => Architect::isPretending(),
            'force' => Architect::isForcing(),
        ]);
    }
}
