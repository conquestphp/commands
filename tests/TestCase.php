<?php

namespace Conquest\Assemble\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Conquest\Assemble\AssembleServiceProvider;
use Conquest\Assemble\Console\Commands\ConquestMakeCommand;
use Workbench\App\Providers\WorkbenchServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            AssembleServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Perform any environment setup here
    }
}