<?php

namespace Conquest\Assemble\Console\Commands;

use Conquest\Assemble\Concerns\HasNames;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Conquest\Assemble\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RouteMakeCommand extends Command
{
    use HasNames;

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
            // ['namespace', 'n', InputOption::VALUE_NONE, 'Indicate whether the namespace should be used for the route name'],
            ['model', 'm', InputOption::VALUE_NONE, 'Indicates the model to bind to'],
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
        return 'App\Http\Controllers\\' . $controller;
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

    /**
     * Get the HTTP method for the route.
     * 
     * @param  string  $method
     * @return string
     */
    protected function getHttpMethod($method)
    {
        return match (Str::lower($method)) {
            'store' => 'post',
            'update' => 'patch',
            'destroy' => 'delete',
            default => 'get',
        };
    }

    protected function getRouteContent($controller)
    {
        // Remove the Controller suffix from the controller name

        // Retrieve the method name which should be suffixed to the controller using camel case
        $name = Str::replaceLast('Controller', '', $controller);
        $method = $this->getMethodName($name);
        $httpMethod = $this->getHttpMethod($method);

        // Generate the route path
        $routePath = $this->getRoutePath($name);
        $routeName = $this->getRouteName($name, $method);

        return sprintf("\nRoute::%s('/%s', %s::class)->name(%s);", $httpMethod, $routePath, $controller, $routeName);
    }

    protected function getRoutePath($name)
    {
        dd($name);
        return Str::kebab($name);
    }

    protected function getRouteName($name, $method)
    {
        return Str::kebab($name . '.' . $method);
    }

    /**
     * Get the method name from the controller.
     *
     * @param  string  $controller
     * @return string
     */
    protected function getMethodName($controller)
    {
        $parts = explode('\\', $controller);
        $className = end($parts);
        
        preg_match('/[A-Z][a-z]+$/', $className, $matches);
        
        return $matches[0];
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
        dd($this->getMethodName($controller));

        if (!file_exists($file)) {
            $this->components->error(sprintf('Route file [%s] does not exist.', $file));
            return false;
        }

        $controllerNamespace = $this->resolveControllerNamespace($controller);
        if (!class_exists($controllerNamespace)) {
            $this->components->error(sprintf('Controller [%s] does not exist.', $controller));
            return false;
        }

        // Open the selected route file and append the new route
        $content = file_get_contents($file);
        $content .= "\n\nuse {$controllerNamespace};";
        $content .= $this->getRouteContent($controller);

        file_put_contents($file, $this->sortImports($content));

        $this->components->info(sprintf('Route for controller [%s] created successfully.', $controller));
    }

}