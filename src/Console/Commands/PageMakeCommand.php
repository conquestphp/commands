<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class PageMakeCommand extends GeneratorCommand
{
    protected $name = 'make:page';
    protected $description = 'Create a new page';
    protected $type = 'Page';

    protected function getStub()
    {
        if ($this->option('form')) {
            return $this->resolveStubPath('/stubs/conquest.page.form.stub');
        }
        return $this->resolveStubPath('/stubs/conquest.page.stub');
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
        return $rootNamespace.config('assemble.paths.page');
    }

    protected function qualifyClass($name)
    {
        return parent::qualifyClass($name);
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite the page even if the page already exists'],
            ['form', 'f', InputOption::VALUE_NONE, 'Generate a form page'],
        ];
    }
}
