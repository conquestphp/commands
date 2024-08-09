<?php

namespace Conquest\Assemble\Console\Commands;

use Conquest\Assemble\Concerns\HasNames;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Console\Concerns\CreatesMatchingTest;

#[AsCommand(name: 'make:conquest')]
class ConquestMakeCommand extends GeneratorCommand
{
    use HasNames;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Conquest';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:conquest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accelerate the creation of CRUD applications.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/conquest.controller.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'o', InputOption::VALUE_NONE, 'Create the class(es) even if they already exist'],
            ['modal', 'M', InputOption::VALUE_NONE, 'Create a new modal for the endpoint'],
            ['page', 'P', InputOption::VALUE_NONE, 'Create a new page for the endpoint'],
            ['form', 'F', InputOption::VALUE_NONE, 'Indicates whether the generated page or modal should be a form'],
            ['model', 'm', InputOption::VALUE_NONE, 'Create a new model'],
            ['policy', 'p', InputOption::VALUE_NONE, 'Create a new policy for the model'],
            ['migration', 'g', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Create a new resource for the model'],
            ['crud', 'c', InputOption::VALUE_NONE, 'Generate endpoints for CRUD operations'],
            ['web', 'w', InputOption::VALUE_NONE, 'Indicates whether the generated controller should be added to the web.php or specified route file'],
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a model, policy, migration, seeder, factory, resource, pages/modals and 8 endpoints which are added to the routes'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
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

        [$name, $method] = $this->parseName($this->getNameInput());

        if ($this->option('model')) {
            $this->call('make:model', [
                'name' => $this->getClassName($name),
                '--force' => $this->option('force'),
                '--factory' => $this->option('factory'),
                '--seed' => $this->option('seed'),
                '--migration' => $this->option('migration'),
                '--policy' => $this->option('policy'),
            ]);
        }


        if ($this->option('resource')) {
            $this->call('make:resource', [
                'name' => $name,
                '--force' => $this->option('force'),
            ]);
        }

        if ($this->option('crud')) {
            foreach(self::METHODS as $method) {
                if (!$this->createConquest($name, $method)) {
                    return false;
                }
            }
        } else {
            if (!$this->createConquest($name, $method)) {
                return false;
            }
        }

        if ($this->option('web')) {
            // If web is applied, use the generated controllers and add it into web.php
            // Or the specified argument value inside routes/
            // $this->createRoutes($name, $method);
        }

        $this->components->success('All Conquest components created successfully.');

        return true;
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
    protected function buildConquestClass($name, $method)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceNamespace($stub, $name)
            ->replaceRequest($stub, $name, $method)
            ->replaceInertia($stub, $name)
            ->replaceModel($stub, $name)
            ->replaceInvoke($stub, $name, $method)
            ->replaceResponse($stub, $name, $method)
            ->replaceClass($stub, $this->getFullName($this->getClassName($name), $method));
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
     * Replace the request import to use the generated request.
     *
     * @param  string  $stub
     * @param  string  $name
     * @param  ?string  $method
     * @return static
     */
    protected function replaceRequest(&$stub, $name, $method): static
    {
        $stub = str_replace(['DummyRequest', '{{ request }}', '{{request}}'], $this->getRequestNamespace($name, $method), $stub);

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
    protected function isModalMethod($method): bool
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
    protected function isPageMethod($method): bool
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
    protected function isInertiaMethod($method): bool
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
    protected function replaceInertia(&$stub, $method): static
    {
        if ($this->isInertiaMethod($method)) {
            $stub = str_replace(['{{ inertia }}', '{{inertia}}'], 'use Inertia\Inertia;', $stub);
        } else {
            $stub = str_replace(['{{ inertia }}', '{{inertia}}'], '', $stub);
        }

        return $this;
    }

    /**
     * Import the model if needed.
     *
     * @param  string  $stub
     * @param  string  $method
     * @return static
     */
    protected function replaceModel(&$stub, $name): static
    {
        if ($this->option('model')) {
            $stub = str_replace(['{{ model }}', '{{model}}'], 'use ' . $this->getModelNamespace($name) . ';', $stub);
        } else {
            $stub = str_replace(['{{ model }}', '{{model}}'], '', $stub);
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
    protected function replaceInvoke(&$stub, $name, $method): static
    {
        $request = $this->getRequest($name, $method);

        if ($this->option('model')) {
            $model = $this->getModel($name);
            $stub = str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request, ' . $model . ' ' . Str::camel($model) . ')', $stub);
        } else {
            $stub = str_replace(['{{ invoke }}', '{{invoke}}'], 'public function __invoke(' . $request . ' $request)', $stub);
        }

        return $this;
    }

    /**
     * Generate the response for the controller.
     *
     * @param  string  $stub
     * @param  string  $name
     * @param  ?string  $method
     * @return static
     */
    protected function replaceResponse(&$stub, $name, $method): static
    {
        $model = $this->getModel($name);
        $props = $this->option('model') ? $model . ' => $' . Str::camel($model) . ",\n]" : '';
        $resource = $this->getResource($name, $method);

        if ($this->isPageMethod($name)) {
            $stub = str_replace(['{{ response }}', '{{response}}'], "return Inertia::render('" . $resource . "', [\n\t'" . $props . ",\n]);",  $stub);
        } else if ($this->isModalMethod($name)) {
            $stub = str_replace(['{{ response }}', '{{response}}'], "return Inertia::modal('" . $resource . "', [\n\t'" . $props . ",\n])->baseRoute('" . config('assemble.base_route') . "');",  $stub);
        } else {
            $stub = str_replace(['{{ response }}', '{{response}}'], 'return back();',  $stub);
        }
        return $this;
    }

    /**
     * Create a Conquest controller and request pair, and any Javascript resources.
     * 
     * @param  string  $name
     * @param  ?string  $method
     */
    protected function createConquest($name, $method)
    {
        
        $controllerPath = $this->getControllerNamespace($name, $method);
        $path = $this->getPath($controllerPath);

        if (!$this->option('force') && $this->alreadyExists($controllerPath)) {
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

        $this->components->info(sprintf('Controller [%s] created successfully.', $path));

        $this->call('make:request', [
            'name' =>  $this->getRequest($name, $method),
            '--force' => $this->option('force'),
        ]);

        if ($this->option('page')) {
            $this->call('make:page', [
                'name' => $this->getResource($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->option('modal')) {
            $this->call('make:modal', [
                'name' => $this->getResource($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->isPageMethod($name)) {
            $this->call('make:page', [
                'name' => $this->getResource($name, $method),
                '--force' => $this->option('force'),
            ]);
        } else if ($this->isModalMethod($name)) {
            $this->call('make:modal', [
                'name' => $this->getResource($name, $method),
                '--force' => $this->option('force'),
            ]);
        }

        return true;
    }
}
