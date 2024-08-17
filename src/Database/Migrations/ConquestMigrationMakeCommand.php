<?php

declare(strict_types=1);

namespace Conquest\Command\Database\Migrations;

use Conquest\Command\Database\Migrations\ConquestMigrationCreator;
use Conquest\Command\Enums\SchemaColumn;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'conquest:migration', description: 'Create a new migration file.')]
class ConquestMigrationMakeCommand extends Command implements PromptsForMissingInput
{
    /**
     * Required base column for the schema.
     * 
     * @var string
     */
    protected $id = "\$table->id();";

    /**
    * The migration creator instance.
    *
    * @var \Illuminate\Database\Migrations\ConquestMigrationCreator
    */
    protected $creator;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @return void
     */
    public function __construct(ConquestMigrationCreator $creator)
    {
        parent::__construct();
        $this->creator = $creator;
    }

    public function handle()
    {
        $this->creator->setContent($this->getColumns());
        $file = $this->creator->create(
            $this->getFileName(), base_path('migrations'), $this->getClassName(), true
        );

        $this->components->info(sprintf('Migration [%s] created successfully.', $file));    
    }

    protected function getColumns()
    {
        if (! $this->option('attributes')) {
            return $this->id;
        }

        dd(str($this->option('attributes'))->explode(',')
            ->map(fn ($column) => trim($column))
            ->map(fn ($column) => $this->getSchema($column))
            ->filter(fn ($column) => $column !== null)
            ->sortBy(fn (array $column) => $column[0]->precedence()), descending: false);

        return str($this->option('attributes'))->explode(',')
            ->map(fn ($column) => trim($column))
            ->map(fn ($column) => $this->getSchema($column))
            ->filter(fn ($column) => $column !== null)
            ->sortBy(fn (array $column) => $column[0]->precedence())
            ->map(fn (array $column) => $column[0]->blueprint($column[1]))
            ->implode("\n") . $this->id;
    }

    /**
     * Get the schema for a given column.
     *
     * @param string $column The column name to get the schema for.
     * @return array{0: SchemaColumn, 1: string} An array containing the SchemaColumn enum and the original column name.
     */
    protected function getSchema(string $column): array
    {
        $schema = SchemaColumn::tryWithPatterns($column);

        if ($coalesced = $schema->coalesced()) {
            $this->components->warn(sprintf('Column [%s] will be coalesced to [%s].', $column, $coalesced));
        } elseif ($schema->isUndefined()) {
            $confirmed = confirm(sprintf('Column [%s] is not a predefined column. Do you want to include it anyway?', $column));
            if (! $confirmed) {
                return null;
            }
        }

        return [$schema, $column];
    }

    // protected function getStubPath()
    // {
    //     return __DIR__.'/stubs/migration.stub';
    // }

    protected function getPath()
    {
        return database_path('migrations');
    }

    protected function getClassName(): string
    {
        return str($this->getNameInput())
            ->plural()
            ->snake()
            ->value();
    }

    protected function getFileName(): string
    {
        return str($this->getNameInput())
            ->snake()
            ->prepend('create_')
            ->append('_table')
            ->value();
    }

    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        if (Str::endsWith($name, '.php')) {
            return Str::substr($name, 0, -4);
        }

        return $name;
    }


    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the migration.'],
        ];            
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the migration even if it already exists'],
            ['attributes', 'a', InputOption::VALUE_REQUIRED, 'The attributes of the migration'],
        ];
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What should the migration be named?',
                'E.g. create_users_table',
            ],
        ];
    }
}