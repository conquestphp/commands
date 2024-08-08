<?php

namespace Conquest\Assemble;

use Conquest\Assemble\Console\Commands\ComponentMakeCommand;
use Conquest\Assemble\Console\Commands\ConquestMakeCommand;
use Conquest\Assemble\Console\Commands\ModalMakeCommand;
use Conquest\Assemble\Console\Commands\PageMakeCommand;
use Illuminate\Support\ServiceProvider;

class AssembleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/assemble.php', 'assemble');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ConquestMakeCommand::class,
                PageMakeCommand::class,
                ModalMakeCommand::class,
                ComponentMakeCommand::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'conquest-stubs');

        $this->publishes([
            __DIR__.'/../config/assemble.php' => config_path('assemble.php'),
        ], 'conquest-config');

    }

    public function provides()
    {
        return [
            ConquestMakeCommand::class,
            PageMakeCommand::class,
            ModalMakeCommand::class,
            ComponentMakeCommand::class
        ];
    }
}