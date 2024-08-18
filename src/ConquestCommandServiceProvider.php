<?php

namespace Conquest\Command;

use Illuminate\Support\ServiceProvider;
use Conquest\Command\Commands\PageMakeCommand;
use Conquest\Command\Commands\RouteAddCommand;
use Conquest\Command\Commands\UserMakeCommand;
use Conquest\Command\Commands\ModalMakeCommand;
use Conquest\Command\Commands\ConquestMakeCommand;
use Conquest\Command\Commands\ComponentMakeCommand;
use Conquest\Command\Database\Migrations\ConquestMigrationCommand;

class ConquestCommandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/conquest-command.php', 'conquest-command');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ConquestMakeCommand::class,
                PageMakeCommand::class,
                ModalMakeCommand::class,
                ComponentMakeCommand::class,
                RouteAddCommand::class,
                UserMakeCommand::class,
                ConquestMigrationCommand::class,
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
            __DIR__.'/../config/conquest-command.php' => config_path('conquest-command.php'),
        ], 'config');

    }
}