<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Concerns\CreatesMatchingTest;

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
        if ($this->isReservedName($this->getNameInput())) {
            $this->components->error('The name "'.$this->getNameInput().'" is reserved by PHP.');
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('model', true);
            $this->input->setOption('factory', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('policy', true);
            $this->input->setOption('resource', true);
            $this->input->setOption('crud', true);
            $this->input->setOption('web', true);
        }

        if ($this->option('model')) {
            $this->call('make:model', [
                'name' => $this->getModel($this->getInputName()),
                '--force' => $this->option('force'),
                '--factory' => $this->option('factory'),
                '--seed' => $this->option('seed'),
                '--migration' => $this->option('migration'),
                '--policy' => $this->option('policy'),
            ]);
        }

        [$name, $method] = $this->parseName($this->getInputName());

        if ($this->option('resource')) {
            $this->call('make:resource', [
                'name' => $name,
                '--force' => $this->option('force'),
            ]);
        }

        if ($this->option('crud')) {
            foreach(self::METHODS as $method) {
                $this->createConquest($name, $method);
            }
        } else {
            $this->createConquest($name, $method);
        }

        if ($this->option('web')) {
            // If web is applied, use the generated controllers and add it into web.php
            // Or the specified argument value inside routes/

            $this->createRoutes($name, $method);
        }
    }

    /**
     * @return array{string, ?string}
     */
    public function parseName(string $name): array
    {
        // Split about the final /
        $system = explode('/', $name);
        $fileName = array_pop($system);

        // Split camel case
        $words = preg_split('/(?=[A-Z])/', $fileName, -1, PREG_SPLIT_NO_EMPTY);
        $finalWord = array_pop($words);

        // Reconstruct the path including the final word
        $name = implode('/', $system);
        $name .= '/' . implode('', $words);

        // Add back the final word to the name if it is not a method
        if (!in_array($finalWord, self::METHODS)) {
            $name .= $finalWord;
        }

        // Check if the final word is in methods, remove it as give it as the type
        $method = in_array($finalWord, self::METHODS) ? $finalWord : null;
        return [$name, $method];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/conquest.controller.stub');
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class(es) even if they already exist'],
            ['modal', 'm', InputOption::VALUE_NONE, 'Create a new modal for the endpoint'],
            ['page', 'P', InputOption::VALUE_NONE, 'Create a new page for the endpoint'],
            ['form', 'F', InputOption::VALUE_NONE, 'Indicates whether the generated page or modal should be a form'],
            ['model', 'o', InputOption::VALUE_NONE, 'Create a new model'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Create a new policy for the model'],
            ['migration', 'g', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Create a new resource for the model'],
            ['crud', 'c', InputOption::VALUE_NONE, 'Generate 8 endpoints for CRUD operations'],
            ['web', 'w', InputOption::VALUE_NONE, 'Indicates whether the generated controller should be added to the web.php or specified route file'],
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a model, policy, migration, seeder, factory, resource, pages/modals and 8 endpoints which are added to the routes'],
        ];
    }

    protected function getNameWithoutNamespace($name)
    {
        return str_replace($this->getNamespace($name).'\\', '', $name);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildConquestClass(string $name, ?string $method)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceRequest($stub, $this->getAppendedName($name, $method))
            ->replaceInertia($stub, $name)
            ->replaceModel($stub, $name)
            ->replaceInvoke($stub, $this->getAppendedName($name, $method))
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
     * @param  ?string  $method
     * @return string
     */
    protected function getRequest(string $name)
    {
        return $this->getNameWithoutNamespace($name.'Request');
    }

    /**
     * Get the request namespace.
     * 
     * @param  string  $name
     * @return string
     */
    protected function getRequestNamespace(string $name)
    {
        return 'App\Http\Requests\\' . $this->getRequest($name);
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
        str_replace(['DummyRequest', '{{ request }}', '{{request}}'], $this->getRequestNamespace($name), $stub);

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
     * @param  string  $method
     * @return bool
     */
    protected function isModalMethod(string $method): bool
    {
        return in_array(
            strtolower($method),
            collect($this->getModalByDefaultMethods())
                ->transform(fn ($method) => strtolower($method))
                ->all()
        );
    }

    /**
     * Checks whether the method renders a page by default.
     *
     * @param  string  $method
     * @return bool
     */
    protected function isPageMethod(string $method): bool
    {
        return in_array(
            strtolower($method),
            collect($this->getPageByDefaultMethods())
                ->transform(fn ($method) => strtolower($method))
                ->all()
        );
    }

    /**
     * Checks whether the given name is reserved.
     *
     * @param  string  $name
     * @return bool
     */
    protected function isInertiaMethod(string $method): bool
    {
        return in_array(
            strtolower($method),
            collect(array_merge($this->getPageByDefaultMethods(), $this->getModalByDefaultMethods()))
                ->transform(fn ($method) => strtolower($method))
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
     * Get the model name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getModel(string $name): string
    {
        return $this->getNameWithoutNamespace($name);
    }

    /**
     * Get the model namespace.
     * 
     * @param  string  $name
     * @return string
     */
    protected function getModelNamespace(string $name): string
    {
        return 'App\\Models\\' . $this->getModel($name);
    }

    /**
     * Import the model if needed.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceModel(string $stub, string $name): static
    {
        if ($this->option('model')) {
            str_replace(['{{ model }}', '{{model}}'], 'use ' . $this->getModelNamespace($name) . ';', $stub);
        } else {
            str_replace(['{{ model }}', '{{model}}'], '', $stub);
        }

        return $this;
    }

    /**
     * Generate the invoke method signature for the controller.
     *
     * @param  string  $stub
     * @param  string  $name
     * @param  ?string  $method
     * @return static
     */
    protected function replaceInvoke(string $stub, string $name): static
    {
        $request = $this->getRequestNamespace($name);

        if ($this->option('model')) {
            $model = $this->getModel($name);
            str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request, ' . $model . ' ' . Str::camel($model) . ')', $stub);
        } else {
            str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request)', $stub);
        }

        return $this;
    }

    /**
     * Get the complete name for the controller.
     *
     * @param  string  $name
     * @param  ?string  $method
     * @return string
     */
    protected function getAppendedName(string $name, ?string $method): string
    {
        return $name . ($method ? $method : '');
    }



    /**
     * Generate the response for the controller.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceResponse(string $stub, string $name): static
    {
        $model = $this->getNameWithoutNamespace($name);
        // $props = $this->$this->option('model')() ? ''.Str::camel($model) .' => $' . Str::camel($model) : '';
        if ($this->isPageMethod($name)) {
            str_replace(['{{ response }}', '{{response}}'], "return Inertia::render('" . $model . "', [\n\t'" . Str::camel($model) . "' => $" . Str::camel($model) . ",\n]);",  $stub);
        } else if ($this->isModalMethod($name)) {
            str_replace(['{{ response }}', '{{response}}'], "return Inertia::modal('" . $model . "', [\n\t'" . Str::camel($model) . "' => $" . Str::camel($model) . ",\n])->baseRoute('" . config('assemble.base_route') . "');",  $stub);
        } else {
            str_replace(['{{ response }}', '{{response}}'], 'return back();',  $stub);
        }
        return $this;
    }


    /**
     * Create a Conquest controller and request pair, and any Javascript resources.
     * 
     * @param  string  $name
     * @param  ?string  $method
     */
    protected function createConquest(string $name, ?string $method)
    {
        // Create out the request first

        $requestName = $this->getRequest($name, $method);
        $this->call('make:request', [
            'name' => $requestName,
            '--force' => $this->option('force'),
        ]);

        // Create the controller using custom stub

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->components->error('Conquest controller already exists.');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildConquestClass($name, $method)));

        if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            $this->handleTestCreation($path);
        }

        if (windows_os()) {
            $path = str_replace('/', '\\', $path);
        }

        $this->components->info(sprintf('Conquest controller [%s] created successfully.', $path));

        // Create the page or modal, or none
        if ($this->option('page')) {
            $this->call('make:page', [
                'name' => $this->getAppendedName($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->option('modal')) {
            $this->call('make:modal', [
                'name' => $this->getAppendedName($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->isPageMethod($name)) {
            $this->call('make:page', [
                'name' => $this->getAppendedName($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->isModalMethod($name)) {
            $this->call('make:modal', [
                'name' => $this->getAppendedName($name, $method),
                '--force' => $this->option('force'),
            ]);
        }
    }
}
