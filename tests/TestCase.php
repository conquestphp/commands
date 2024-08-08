<?php

namespace Conquest\Assemble\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Conquest\Assemble\AssembleServiceProvider;
use Workbench\App\Providers\WorkbenchServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('vendor:publish', [
            '--provider' => AssembleServiceProvider::class,
            '--tag' => 'assemble-stubs',
        ])->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            AssembleServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }
}
