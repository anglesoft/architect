<?php

namespace Angle\Architect;

use Illuminate\Support\ServiceProvider;

class ArchitectServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    // protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\SprintCommand::class,
                Console\MakeSprintCommand::class,
                Console\ArchitectInstallCommand::class
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

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/architect.php' => config_path('architect.php'),
            ], 'architect-config');
        }
    }
}
