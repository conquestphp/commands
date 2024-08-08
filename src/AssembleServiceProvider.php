<?php

namespace Conquest\Assemble;

use Conquest\Assemble\Console\Commands\ModalMakeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommands(
                ModalMakeCommand::class
            );
    }
}
