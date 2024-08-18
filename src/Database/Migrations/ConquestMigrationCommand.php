<?php

declare(strict_types=1);

namespace Conquest\Command\Database\Migrations;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Conquest\Command\Enums\SchemaColumn;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Conquest\Command\Database\Migrations\ConquestMigrationCreator;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

#[AsCommand(name: 'conquest:migration', description: 'Create a new migration file.')]
class ConquestMigrationCommand extends Command implements PromptsForMissingInput
{
    /**
     * Whether the user has confirmed undefined columns during prompting.
     * 
     * @var bool
     */
    protected $confirmedDuringPrompting = false;

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
        if (! $this->option('columns')) {
            return $this->id;
        }
            
        return str(str($this->option('columns'))->explode(',')
            ->map(fn ($column) => trim($column))
            ->map(fn ($column) => $this->getSchema($column))
            ->filter(fn ($column) => $column !== null)
            ->sortByDesc(fn (array $column) => $column[0]->precedence())
            ->map(fn (array $column) => "\t\t\t" . $column[0]->blueprint($column[1]))
            ->implode("\n"))
            ->prepend($this->id . "\n")
            ->value();
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
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the migration even if it already exists.'],
            ['columns', 'c', InputOption::VALUE_REQUIRED, 'The columns of the migration.'],
            ['suppress', 's', InputOption::VALUE_NONE, 'Suppress the confirmation prompts.'],
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

        /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->didReceiveOptions($input)) {
            return;
        }

        $columns = collect();
        collect(multiselect('Select which columns you would like to include?.', 
            collect(SchemaColumn::cases())
                ->filter(fn (SchemaColumn $column) => !$column->coalesced())
                ->mapWithKeys(fn (SchemaColumn $column) => [$column->value => $column->name])
                ->toArray()
        ))->each(fn ($option) => $columns->push($option));
        
        if ($columns->contains(SchemaColumn::ForeignId->value)) {
            $columns = $columns->reject(fn ($column) => $column === SchemaColumn::ForeignId->value);

            while (true) {
                $value = text('What is the foreign key column?', 'user_id');
                $columns->push($value);

                if (empty($value) || !confirm('Do you want to add another foreign key column?')) {
                    break;
                }
            }                    
        }

        if ($columns->contains(SchemaColumn::Undefined->value)) {
            $columns = $columns->reject(fn ($column) => $column === SchemaColumn::Undefined->value);
            while (true) {
                $value = text('What is the column name?', 'custom');
                $columns->push($value);

                if (empty($value) || !confirm('Do you want to add another column?')) {
                    break;
                }
            }                    
        }

        $this->confirmedDuringPrompting = true;
        $input->setOption('columns', $columns->unique()->implode(','));
    }

}