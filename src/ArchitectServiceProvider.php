<?php

namespace Angle\Architect;

use Angle\Architect\Code\Blueprint;
use Angle\Architect\Code\Builder;
use Angle\Architect\Console\SprintCommand;
use Angle\Architect\Console\MakeSprintCommand;
use Illuminate\Support\ServiceProvider;

class ArchitectServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
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
                SprintCommand::class,
                MakeSprintCommand::class
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
        // $this->registerBuilder();
    }

    /**
     * Register the code builder.
     *
     * @return void
     */
    protected function registerBuilder()
    {
        // $this->app->singleton('architect.sprinter', function ($app) {
        //     return new Builder($app['files']);
        // });
    }

}
