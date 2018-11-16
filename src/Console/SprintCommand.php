<?php

namespace Angle\Architect\Console;

use Angle\Architect\Facades\Architect;
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
        Architect::sprint();

        if ($this->option('pretend') === true)
            Architect::pretend();

        if ($this->option('force') === true)
            Architect::force();

        // dump([
        //     'sprint' => $this->sprint,
        //     'repository' => get_class_methods($this->repository),
        //     'pretending' => Architect::isPretending(),
        //     'forcing' => Architect::isForcing(),
        //     'sprints' => $this->repository->getSprints(),
        //     'ran' => $this->repository->getRan(),
        //     'files' => $this->sprint->getSprintFiles('sprints'),
        // ]);

        $files = $this->sprint->getSprintFiles('sprints');

        $i = 0;

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

            // $class = $sprint->run();

            $sprint->run();

            $stack = Architect::$stack[$i++];

            foreach ($stack as $file) {
                if ($this->option('pretend') == false) {
                    $this->line("<info>Created:</info> {$file}");
                } else {
                    $this->line("<fg=cyan;bg=black>Would create:</> {$file}");
                }
            }

            // Architect::$stack);

            // $this->line("<comment>Creating:</comment> {$class}");

            // $this->sprint->run($name);
        }


        // $this->repository->create($file);

        // Find file(s) to run
        // Compile those files
        // Save batch to db
        // Output result
    }
}
