<?php

namespace Conquest\Assemble\Console\Commands;

use Conquest\Assemble\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:js-component')]
class ComponentMakeCommand extends ResourceGeneratorCommand
{
    use ResolvesStubPath;

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
