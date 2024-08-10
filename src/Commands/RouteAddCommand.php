<?php

namespace Conquest\Assemble\Commands;

use Conquest\Assemble\Concerns\HasMethods;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteAddCommand extends Command implements PromptsForMissingInput
{
    use HasMethods;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'add:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new route from a controller.';

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
            ['class', 'c', InputOption::VALUE_NONE, 'Indicate whether to only use the base class name to generate the route name'],
            ['model', 'm', InputOption::VALUE_NONE, 'Indicates whether to use the base class as a model name'],
            ['file', 'f', InputOption::VALUE_OPTIONAL, 'The name of the route file to append to'],
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
        ];
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getControllerInput()
    {
        $controller = str($this->argument('controller'))->trim();

        if ($controller->endsWith('.php')) {
            $controller = $controller->substr(0, -4);
        }
        // Ensure it ends with Controller
        if (! $controller->endsWith('Controller')) {
            return $controller->append('Controller')->toString();
        }

        return $controller->toString();
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getFileOption()
    {
        if (! ($file = $this->option('file'))) {
            return $this->route_path('web');
        }

        return $this->route_path(str($file)->trim('/')
            ->replace('.php', '')
            ->when(fn ($file) => Str::startsWith($file, 'route/'), fn ($file) => $file->replace('route/', ''))
            ->toString()
        );

    }

    /**
     * Get the path to the route file.
     *
     * @param  string  $file
     * @return string
     */
    protected function route_path($file)
    {
        return $this->laravel->basePath('routes/'.$file.'.php');
    }

    /**
     * Resolve the controller namespace.
     *
     * @param  string  $controller
     * @return string
     */
    protected function resolveControllerNamespace($controller)
    {
        return str($controller)->prepend('App/Http/Controllers/')->toString();
    }

    /**
     * Groups and sorts the imports at the top of the file and routes at the bottom.
     *
     * @param  string  $content
     * @return string
     */
    protected function organiseFileContent($content)
    {
        // Extract and sort imports
        preg_match_all('/^use [^;]+;$/m', $content, $importMatches);
        $imports = $importMatches[0];
        sort($imports);

        // Extract routes
        preg_match_all('/^Route::.*$/m', $content, $routeMatches);
        $routes = $routeMatches[0];

        // Remove existing imports and routes from content
        $content = preg_replace('/^use [^;]+;$/m', '', $content);
        $content = preg_replace('/^Route::.*$/m', '', $content);

        // Remove extra newlines and <?php tags
        $content = preg_replace('/^\s*<\?php\s*$/m', '', $content);
        $content = preg_replace('/^\s*$/m', '', $content);

        // Reconstruct file content
        $newContent = "<?php\n\n".implode("\n", $imports)."\n\n".trim($content)."\n".implode("\n", $routes);

        return trim($newContent);
    }

    protected function getRouteContent($controller)
    {
        return sprintf("\nRoute::%s('/%s', %s::class)->name('%s');\n",
            $this->getHttpMethod($this->getMethodName($controller)),
            $this->getRoutePath($controller),
            $this->getBase($controller),
            $this->getRouteName($controller)
        );
    }

    protected function getRoutePath($controller)
    {
        $method = $this->getMethodName($controller);
        $parts = explode('/', $this->getPureClassName($controller));
        $end = array_pop($parts);

        return trim(collect($parts)->map(fn (string $part) => str($part)->kebab()->singular())
            ->push($this->getFinalRoutePart($end, $method))
            ->implode('/'), '/'
        );      
    }

    /**
     * Get the final route part.
     * 
     * @param  string  $part
     * @param  string  $method
     * @return string
     */
    protected function getFinalRoutePart($part, $method)
    {
        if (!($scoped = $this->isScoped($method))) {
            return strtolower($method) === 'create' ? 'create' : '';
        }

        return str($part)->singular()
                ->when($this->option('model') && $scoped, 
                    fn ($part) => $part->camel()->prepend('{')->append('}'),
                    fn ($part) => $part->kebab()->singular()
                )
                ->when(strtolower($method) === 'create', fn ($part) => $part->append('/create'))
                ->when(strtolower($method) === 'edit', fn ($part) => $part->append('/edit'))
                ->when(strtolower($method) === 'delete', fn ($part) => $part->append('/delete'));
    }

    protected function getRouteName($controller)
    {
        $method = $this->getMethodName($controller);
        $parts = explode('/', $this->getPureClassName($controller));

        if ($this->option('class')) {
            return str(end($parts))->kebab()->singular().'.'.str($method)->kebab();
        }

        return str(collect($parts)
            ->map(fn ($part) => str($part)->kebab()->singular())
            ->implode('.'))
            ->when($method, fn ($route) => $route.'.'.str($method)->kebab());
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
        $file = $this->getFileOption();
        if (! file_exists($file)) {
            $this->components->error(sprintf('Route file [%s] does not exist.', $file));

            return false;
        }

        $namespace = $this->resolveControllerNamespace($controller);
        if (! file_exists(base_path(str($namespace)->lcfirst().'.php'))) {
            $this->components->error(sprintf('Controller [%s] does not exist.', $controller));

            return false;
        }
        $content = file_get_contents($file);
        $content .= sprintf("\n\nuse %s;", str($namespace)->replace('/', '\\'));
        $content .= $this->getRouteContent($controller);
        if (file_put_contents($file, $this->organiseFileContent($content))) {
            $this->components->success(sprintf('Route for controller [%s] created successfully.', $controller));

            return true;
        }
        $this->components->error(sprintf('Route for controller [%s] could not be created.', $controller));

        return false;
    }
}