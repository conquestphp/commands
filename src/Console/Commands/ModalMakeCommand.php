<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModalMakeCommand extends GeneratorCommand
{
    protected $name = 'make:modal';

    protected $description = 'Create a new modal';

    protected $type = 'Modal';

    protected function getStub()
    {
        if ($this->option('form')) {
            return $this->resolveStubPath('/stubs/conquest.modal.form.stub');
        }

        return $this->resolveStubPath('/stubs/conquest.modal.stub');
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
        return $rootNamespace.config('assemble.paths.modal');
    }

    protected function qualifyClass($name)
    {
        return parent::qualifyClass($name);
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite the modal even if the modal already exists'],
            ['form', 'f', InputOption::VALUE_NONE, 'Generate a form modal'],
        ];
    }
}
