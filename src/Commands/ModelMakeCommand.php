<?php

namespace Conquest\Command\Commands;

use Conquest\Command\Concerns\FillsContent;
use Conquest\Command\Concerns\HasSchemaColumns;
use Illuminate\Foundation\Console\ModelMakeCommand as IlluminateModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(name: 'make:conquest-model')]
class ModelMakeCommand extends IlluminateModelMakeCommand
{
    use FillsContent;
    use HasSchemaColumns;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:conquest-model';

    protected $contentPlaceholder = '//';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        return $this->fillContent($stub);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['columns', 'c', InputOption::VALUE_REQUIRED, sprintf('The columns of the %s', str($this->type)->lower()->value())],
            ['suppress', 's', InputOption::VALUE_NONE, 'Suppress the confirmation prompts.'],
        ]);
    }

    public function getContent(): string
    {
        if (!$this->option('columns')) {
            return '//';
        }

        return str($this->getSchemaColumns()
                ->map(fn (array $column) => sprintf('\'%s\' => %s', $column[1], $column[0]->factory($column[1])))
                ->implode("\n\t\t\t")
            )
            ->value();
    }
}
