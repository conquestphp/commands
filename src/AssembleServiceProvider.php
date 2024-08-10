<?php

namespace Conquest\Assemble;

use Conquest\Assemble\Commands\ComponentMakeCommand;
use Conquest\Assemble\Commands\ConquestMakeCommand;
use Conquest\Assemble\Commands\ModalMakeCommand;
use Conquest\Assemble\Commands\PageMakeCommand;
use Conquest\Assemble\Commands\RouteAddCommand;
use Conquest\Assemble\Commands\UserMakeCommand;
use Illuminate\Support\ServiceProvider;

class AssembleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/assemble.php', 'assemble');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ConquestMakeCommand::class,
                PageMakeCommand::class,
                ModalMakeCommand::class,
                ComponentMakeCommand::class,
                RouteAddCommand::class,
                UserMakeCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        $this->publishes([
            __DIR__.'/Commands/stubs' => base_path('stubs'),
        ], 'stubs');

        $this->publishes([
            __DIR__.'/../config/assemble.php' => config_path('assemble.php'),
        ], 'config');

    }
}
