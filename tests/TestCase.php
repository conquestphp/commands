<?php

namespace Conquest\Assemble\Tests;

use Conquest\Assemble\AssembleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
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
        $app['config']->set('workbench', [
            'start' => '/',
            'install' => true,
            'guard' => 'web',
            'discovers' => [
                'web' => true,
                'api' => false,
                'commands' => false,
                'components' => false,
                'views' => false,
            ],
            'build' => [
                'create-sqlite-db',
                'migrate:fresh',
            ],
            'assets' => [],
            'sync' => [],
        ]);
    }
}
