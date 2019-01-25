<?php

namespace Angle\Architect;

use Illuminate\Support\ServiceProvider;

class ArchitectServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\Sprints\MakeCommand::class,
                Console\Sprints\RollbackCommand::class,
                Console\Sprints\SprintCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/architect.php', 'architect'
        );
    }
}
