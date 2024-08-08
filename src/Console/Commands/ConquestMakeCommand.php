<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Support\Str;
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
        if ($this->option('crud') || $this->option('all')) {
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
            ->replaceRequest($stub, $name)
            ->replaceInertia($stub, $name)
            ->replaceModel($stub, $name)
            ->replaceInvoke($stub, $name)
            ->replaceResponse($stub, $name)
            ->replaceClass($stub, $name);
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
        ];
    }

    /**
     * Append request to the name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRequest($name)
    {
        return $name . 'Request';
    }

    /**
     * Replace the request import to use the generated request.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return static
     */
    protected function replaceRequest($stub, $name): static
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        str_replace(['DummyRequest', '{{ request }}', '{{request}}'], $class, $stub);

        return $this;
    }

    protected function getModalByDefaultMethods()
    {
        return [
            'Delete'
        ];
    }

    protected function getPageByDefaultMethods()
    {
        return [
            'Index',
            'Show',
            'Create',
            'Edit',
        ];
    }

    /**
     * Checks whether the method renders a modal by default.
     *
     * @param  string  $name
     * @param  bool  $force
     * @return bool
     */
    protected function isModalMethod($name, $force = false)
    {
        return $force || in_array(
            strtolower($name),
            collect($this->getModalByDefaultMethods())
                ->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Checks whether the method renders a page by default.
     *
     * @param  string  $name
     * @param  bool  $force
     * @return bool
     */
    protected function isPageMethod($name, $force = false)
    {
        return $force || in_array(
            strtolower($name),
            collect($this->getPageByDefaultMethods())
                ->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isInertiaMethod($name)
    {
        return in_array(
            strtolower($name),
            collect(array_merge($this->getPageByDefaultMethods(), $this->getModalByDefaultMethods()))
                ->transform(fn ($name) => strtolower($name))
                ->all()
        );
    }

    /**
     * Import Inertia if needed.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceInertia($stub, $method): static
    {
        if ($this->isInertiaMethod($method)) {
            str_replace(['{{ inertia }}', '{{inertia}}'], 'use Inertia\Inertia;', $stub);
        } else {
            str_replace(['{{ inertia }}', '{{inertia}}'], '', $stub);
        }

        return $this;
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @return bool
     */
    protected function usesModel()
    {
        return $this->option('model');
    }

    /**
     * Get the model name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getModel($name)
    {
        // Return the model path
        return 'App\\Models\\' . $this->getNameWithoutNamespace($name);
    }

    protected function getNameWithoutNamespace($name)
    {
        return str_replace($this->getNamespace($name).'\\', '', $name);
    }

    /**
     * Import the model if needed.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceModel($stub, $name): static
    {
        if ($this->usesModel()) {
            str_replace(['{{ model }}', '{{model}}'], 'use ' . $this->getModel($name) . ';', $stub);
        } else {
            str_replace(['{{ model }}', '{{model}}'], '', $stub);
        }

        return $this;
    }

    /**
     * Generate the invoke method signature for the controller.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceInvoke($stub, $name): static
    {
        $request = $this->getNameWithoutNamespace($this->getRequest($name));

        if ($this->usesModel()) {
            $model = $this->getNameWithoutNamespace($name);
            str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request, ' . $model . ' ' . Str::camel($model) . ')', $stub);
        } else {
            str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request)', $stub);
        }

        return $this;
    }

    protected function getResource($name)
    {
        return $this->getNameWithoutNamespace($name);
    }

    /**
     * Generate the response for the controller.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceResponse($stub, $name): static
    {
        $model = $this->getNameWithoutNamespace($name);
        $props = $this->usesModel() ? ''.Str::camel($model) .' => $' . Str::camel($model) : '';
        if ($this->isPageMethod($name)) {
            str_replace(['{{ response }}', '{{response}}'], "return Inertia::render('" . $this->getResource($name) . "', [\n\t'" . Str::camel($model) . "' => $" . Str::camel($model) . ",\n]);",  $stub);
        } else if ($this->isModalMethod($name)) {
            str_replace(['{{ response }}', '{{response}}'], "return Inertia::modal('" . $this->getResource($name) . "', [\n\t'" . Str::camel($model) . "' => $" . Str::camel($model) . ",\n])->baseRoute('" . config('assemble.base_route') . "');",  $stub);
        } else {
            str_replace(['{{ response }}', '{{response}}'], 'return back();',  $stub);
        }
        return $this;
    }


    protected function createConquest($type, $name, bool $modal = false, bool $page = false)
    {
        // Create out the request first
        $requestName = $this->getRequest($name);
        $this->call('make:request', [
            'name' => $requestName,
            '--force' => $this->option('force'),
        ]);        


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

    }
}
