<?php

namespace Conquest\Assemble\Console\Commands;

use Conquest\Assemble\Concerns\ResolvesStubPath;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

abstract class ResourceGeneratorCommand extends GeneratorCommand
{
    use ResolvesStubPath;

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

        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->components->error($this->type.' already exists.');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $info = $this->type;

        if (windows_os()) {
            $path = str_replace('/', '\\', $path);
        }

        $this->components->info(sprintf('%s [%s] created successfully.', $info, $path));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $extension = config('assemble.extension', 'vue');
        $name = trim(str_replace('.'.$extension, '', $name));

        return resource_path(trim($this->rootNamespace(), '/').'/'.str_replace('\\', '/', $name).'.'.$extension);
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        return $name;
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
        return $this->files->get($this->getStub());
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
                'What should the '.strtolower($this->type).' be named?',
                'E.g. UserEdit',
            ],
        ];
    }
}
