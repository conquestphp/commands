<?php

namespace Conquest\Assemble\Console\Commands;

use Conquest\Assemble\Concerns\HasMethods;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RouteMakeCommand extends Command
{
    use HasMethods;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Append a new route from a controller.';

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What invokable controller should the route use?',
                'E.g. UserEdit',
            ],
        ];
    }

    protected function getOptions()
    {
        return [
            ['structure', 's', InputOption::VALUE_NONE, 'Indicate whether the only the class name should be used to generate the route name'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Indicates the model to bind to'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The name of the controller inside the app/Http/Controllers directory'],
            ['file', InputArgument::OPTIONAL, 'The name of the route file to append to'],
        ];
    }

    protected function getModelInput()
    {
        if ($model = $this->option('model')) {
            return str($model)->replace('/', '\\')->replace('App\\Models', '')->ltrim('\\')->replace('.php', '')->toString();
        }

        return null;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getControllerInput()
    {
        $controller = trim($this->argument('controller'));

        if (Str::endsWith($controller, '.php')) {
            $controller = Str::substr($controller, 0, -4);
        }
        // Ensure it ends with Controller
        if (!Str::endsWith($controller, 'Controller')) {
            return $controller . 'Controller';
        }


        return $controller;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getFileInput()
    {
        $file = trim($this->argument('file'));

        // If no file is provided, use the default route file.
        if (empty($file)) {
            return $this->route_path('web');
        }
        
        $file = trim($file, '/');

        if (Str::endsWith($file, '.php')) {
            $file = Str::substr($file, 0, -4);
        }

        if (Str::startsWith($file, 'route/')) {
            $file = Str::substr($file, 6);
        }

        return $file;
    }

    /**
     * Get the path to the route file.
     *
     * @param  string  $file
     * @return string
     */
    protected function route_path($file)
    {
        return base_path('routes/' . $file . '.php');
    }

    protected function resolveControllerNamespace($controller)
    {
        return 'App\\Http\\Controllers\\' . $controller;
    }

    /**
     * Alphabetically sorts the imports for the file.
     *
     * @param  string  $stub
     * @return string
     */
    protected function sortImports($stub)
    {
        if (preg_match('/(?P<imports>(?:^use [^;{]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", trim($match['imports']));

            sort($imports);

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }


    protected function getRouteContent($controller)
    {
        $httpMethod = $this->getHttpMethod($this->getMethodName($controller));

        // Generate the route path
        $routePath = $this->getRoutePath($controller);
        $routeName = $this->getRouteName($controller);

        return sprintf("\nRoute::%s('/%s', %s::class)->name(%s);\n", $httpMethod, $routePath, $controller, $routeName);
    }

    protected function getRoutePath($controller)
    {
        $parts = explode('/', $this->getPureClassName($controller));

        return collect($parts)
            ->map(fn ($part) => str($part)->kebab()->singular())
            ->when($model = $this->getModelInput(), fn ($collection) => $collection->push(str($model)->camel()->singular()->prepend('{')->append('}')))
            ->implode('/');
    }

    protected function getRouteName($controller)
    {
        $method = $this->getMethodName($controller);
        $parts = explode('/', $this->getPureClassName($controller));

        if ($this->option('structure')) {
            return str(end($parts))->kebab()->singular() . '.' . str($method)->kebab();
        }

        return collect($parts)
            ->map(fn ($part) => str($part)->kebab()->singular())
            ->implode('.')
            . '.'
            . str($method)->kebab();
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
        $controller = $this->getControllerInput();
        $file = $this->getFileInput();

        dd($this->getRoutePath($controller));

        if (!file_exists($file)) {
            $this->components->error(sprintf('Route file [%s] does not exist.', $file));
            return false;
        }

        $controllerNamespace = $this->resolveControllerNamespace($controller);
        if (!class_exists($controllerNamespace)) {
            $this->components->error(sprintf('Controller [%s] does not exist.', $controller));
            return false;
        }

        $content = file_get_contents($file);
        $content .= sprintf("\n\nuse %s;", $controllerNamespace);
        $content .= $this->getRouteContent($controller);

        file_put_contents($file, $this->sortImports($content));

        $this->components->info(sprintf('Route for controller [%s] created successfully.', $controller));
    }

}