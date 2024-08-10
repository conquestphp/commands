<?php

namespace Conquest\Assemble\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:js-component')]
class ComponentMakeCommand extends ResourceGeneratorCommand
{
    protected $name = 'make:js-component';

    protected $description = 'Create a new component';

    protected $type = 'Component';

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/conquest.component.stub');
    }

    protected function rootNamespace()
    {
        return config('assemble.paths.component', 'js/Components');
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the component even if the component already exists'],
        ];
    }
}
