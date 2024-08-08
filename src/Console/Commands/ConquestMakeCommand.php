<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ConquestMakeCommand extends Command
{
    public const METHODS = [
        'Index',
        'Show',
        'Create',
        'Store',
        'Edit',
        'Update',
        'Delete',
        'Destroy'
    ];

    public const PURGE = [
        'Controller',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:conquest {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate endpoints using opinionated names';

    /**
     * Retrieve the name to use.
     */
    protected function getInputName()
    {
        return $this->argument('name');
    }

    public function handle()
    {
        [$path, $method] = $this->parseName($this->getInputName());
        // Build Conquest controller

        // Build request

        // Check to build model and associatives

        // Check to bui
    }

    /**
     * @return array{string, string}
     */
    public function parseName(string $name): array
    {
        // Split about the final /
        $system = explode('/', $name);
        // If the last element in array is in PURGE, remove it
        $fileName = array_pop($system);

        // Split camel case
        $final = preg_split('/(?=[A-Z])/', $fileName, -1, PREG_SPLIT_NO_EMPTY);
        dd($final);

        // Check if final name is a method -> controller build
        // or a
        return [
            '',
            ''
        ];

    }

    /**
     * Always executed
     */
    protected function handleController()
    {
        

    }

    /**
     * Always executed
     */
    protected function handleRequest()
    {

    }

    /**
     * Requires model flag and value
     */
    protected function handleModel()
    {

    }

    /**
     * Requires flag and model
     */
    protected function handlePolicy()
    {

    }

    /**
     * Requires flag and model
     */
    protected function handleMigration()
    {

    }

    /**
     * Requires flag
     */
    protected function handleSeeder()
    {

    }

    protected function handleFactory()
    {

    }

    protected function handleResource()
    {
        
    }

    protected function handleCrud()
    {

    }

    protected function handleRoute()
    {
        // app('routes')

    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite existing files with the ones to be created'],
            ['modal', 'm', InputOption::VALUE_NONE, 'Generate a modal'],
            ['page', 'P', InputOption::VALUE_NONE, 'Generate a page'],
            ['form', 'F', InputOption::VALUE_NONE, 'Generate the page or modal as a form'],
            ['model', 'o', InputOption::VALUE_NONE, 'Generate a model'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Generate a policy'],
            ['migration', 'g', InputOption::VALUE_NONE, 'Generate a migration'],
            ['seeder', 's', InputOption::VALUE_NONE, 'Generate a seeder'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Generate a factory'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource'],
            ['crud', 'c', InputOption::VALUE_NONE, 'Generate 8 endpoints for CRUD operations'],
            ['web', 'w', InputOption::VALUE_NONE, 'Use the namespace to append the created endpoint to the provided file or web.php'],
            ['all', 'a', InputOption::VALUE_NONE, 'Generate all options'],
        ];
    }
}
