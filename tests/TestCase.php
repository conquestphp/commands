<?php

namespace Conquest\Assemble\Tests;

use Conquest\Assemble\AssembleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Orchestra\Testbench\Attributes\WithMigration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Providers\WorkbenchServiceProvider;
use function Orchestra\Testbench\artisan; 

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->artisan('migrate', ['--database' => 'testing'])->run();
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
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}