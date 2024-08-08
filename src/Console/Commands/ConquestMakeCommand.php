<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:conquest')]
class ConquestMakeCommand extends GeneratorCommand
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
        $controllerName = $path . 'Controller';
        $controllerPath = app_path('Http/Controllers/' . $controllerName . '.php');
        
        // Get the custom stub content
        $stubPath = base_path('stubs/controller.conquest.stub');
        $stubContent = file_get_contents($stubPath);
        
        // Replace placeholders in the stub
        $stubContent = str_replace('{{ class }}', class_basename($controllerName), $stubContent);
        $stubContent = str_replace('{{ namespace }}', 'App\\Http\\Controllers\\' . str_replace('/', '\\', dirname($controllerName)), $stubContent);
        
        // Ensure the directory exists
        if (!file_exists(dirname($controllerPath))) {
            mkdir(dirname($controllerPath), 0755, true);
        }
        
        // Write the controller file
        file_put_contents($controllerPath, $stubContent);
        
        $this->info('Controller created successfully: ' . $controllerPath);

        // Build request

        // Check to build model and associatives

        // Check to bui
    }

    /**
     * @return array{?string, string}
     */
    public function parseName(string $name): array
    {
        // Split about the final /
        $system = explode('/', $name);
        $fileName = array_pop($system);

        // Split camel case
        $words = preg_split('/(?=[A-Z])/', $fileName, -1, PREG_SPLIT_NO_EMPTY);
        $finalWord = array_pop($words);

        // Handle 'Controller' case
        if (in_array($finalWord, self::PURGE)) {
            $finalWord = array_pop($words);
            if (empty($words)) {
                throw new \InvalidArgumentException("Invalid name: '{$name}'. Cannot end with 'Controller' if no other words are present.");
            }
        }

        // Reconstruct the path including the final word
        $path = implode('/', $system);
        $path .= '/' . implode('', $words);

        // Add back the final word to the path
        if (!in_array($finalWord, self::METHODS)) {
            $path .= $finalWord;
        }

        // Check if 'crud' or 'all' option is enabled
        if ($this->hasOption('crud') || $this->hasOption('all')) {
            return [null, $path];
        }

        // Check if the final word is in METHODS
        $type = in_array($finalWord, self::METHODS) ? $finalWord : null;
        
        return [$type, $path];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/controller.conquest.stub');
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

/**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name)
            ->replaceRequest($stub, $name)
            ->replaceInertia($stub, $name)
            ->replaceModel($stub, $name)
            ->replaceInvoke($stub, $name)
            ->replaceResponse($stub, $name);
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What argument should be used as the generator for this conquest command?',
                'E.g. UserEdit'
            ]
        ]
    }
}
