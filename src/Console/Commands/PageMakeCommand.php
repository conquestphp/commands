<?php

namespace Conquest\Assemble\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Conquest\Assemble\Concerns\ResolvesStubPath;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\multiselect;


class PageMakeCommand extends GeneratorCommand
{
    use ResolvesStubPath;

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
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the page even if the page already exists'],
            ['form', 'F', InputOption::VALUE_NONE, 'Indicates whether the generated page should be a form'],
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
