<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Support\Str;
use function Laravel\Prompts\multiselect;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:conquest')]
class ModalMakeCommand extends ResourceGeneratorCommand
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

    protected function rootNamespace()
    {
        return config('assemble.paths.modals', 'js/Modals');
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the modal even if the modal already exists'],
            ['form', 'F', InputOption::VALUE_NONE, 'Indicates whether the generated modal should be a form'],
        ];
    }

     /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        collect(multiselect('Would you like any of the following?', [
            'form' => 'As form',
            'force' => 'Force creation',
        ]))->each(fn ($option) => $input->setOption($option, true));
    }
}
