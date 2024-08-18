<?php

namespace Illuminate\Database\Console\Factories;

use Conquest\Command\Concerns\FillsContent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand as IlluminateFactoryMakeCommand;

#[AsCommand(name: 'make:conquest-factory')]
class FactoryMakeCommand extends IlluminateFactoryMakeCommand
{
    use FillsContent;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:conquest-factory';

    protected $contentPlaceholder = '//';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        return $this->fillContent(parent::buildClass($name));
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

    /**
     * Get the schema for a given column.
     *
     * @param string $column The column name to get the schema for.
     * @return null|array{0: SchemaColumn, 1: string} An array containing the SchemaColumn enum and the original column name.
     */
    protected function getSchema(string $column): ?array
    {
        $schema = SchemaColumn::tryWithPatterns($column);

        if ($this->option('suppress')) {
            // Do nothing
        } elseif ($coalesced = $schema->coalesced()) {
            $this->components->warn(sprintf('Column [%s] will be coalesced to [%s].', $column, $coalesced));
        } elseif ($schema->isUndefined() && ! $this->confirmedDuringPrompting) {
            if (! confirm(sprintf('Column [%s] is not a predefined column. Do you want to include it anyway?', $column))) {
                return null;
            }
        }

        return [$schema, $column];
    }

    public function getContent(): string
    {
        if (!$this->option('columns')) {
            return '//';
        }

        return str(str($this->option('columns'))
            ->trim()
            ->explode(',')
            ->map(fn ($column) => trim($column))
            ->map(fn ($column) => $this->getSchema($column))
            ->filter(fn ($column) => $column !== null)
            ->sortByDesc(fn (array $column) => $column[0]->precedence())
            ->map(fn (array $column) => "\t\t\t" . $column[0]->blueprint($column[1]))
            ->implode("\n"))
            ->prepend($this->id . "\n")
            ->value();
    }
}
