<?php

declare(strict_types=1);

namespace Conquest\Command\Commands;

use Conquest\Command\Database\Migrations\ConquestMigrationCreator;
use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Console\Migrations\TableGuesser;

#[AsCommand(name: 'make:conquest-migration', description: 'Create a new migration file.')]
class ConquestMigrationMakeCommand extends Command implements PromptsForMissingInput
{
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
        $this->creator->setContentPlaceholder('$table->id();');
        $this->creator->afterCreate(fn ($table, $path) => $this->fillContent());
    }

    public function handle()
    {
        $name = Str::snake(trim($this->input->getArgument('name')));

        $this->creator->setContent($this->getMigrationColumns());

        $file = $this->creator->create(
            $name, database_path('migrations'), $table, true
        );

        $this->components->info(sprintf('Migration [%s] created successfully.', $file));    
    }

    protected function getMigrationColumns()
    {
        $columns = array_map('trim', explode(',', $this->option('attributes')));
        dd($columns);

        if (empty($columns)) {
            return '';
        }
    }

    // protected function getStubPath()
    // {
    //     return __DIR__.'/stubs/migration.stub';
    // }

    protected function getPath()
    {
        return database_path('migrations');
    }

    protected function getFormattedName(): string
    {
        return str(class_basename($this->getNameInput()))
            ->pluralStudly()
            ->snake()
            ->toString();
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