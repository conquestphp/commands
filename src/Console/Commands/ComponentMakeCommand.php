<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ComponentMakeCommand extends GeneratorCommand
{
    protected $name = 'make:component';

    protected $description = 'Create a new component';

    protected $type = 'Component';

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/conquest.component.stub');
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return resource_path().'/'.str_replace('\\', '/', $name).'.'.config('assemble.extension');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../..'.$stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.config('assemble.paths.component');
    }

    protected function qualifyClass($name)
    {
        return parent::qualifyClass($name);
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite the component even if the component already exists'],
        ];
    }
}
