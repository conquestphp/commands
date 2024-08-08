<?php

namespace Conquest\Assemble;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Conquest\Assemble\Commands\AssembleCommand;

class AssembleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('assemble')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_assemble_table')
            ->hasCommand(AssembleCommand::class);
    }
}
