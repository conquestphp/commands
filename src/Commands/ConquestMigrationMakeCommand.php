<?php

declare(strict_types=1);

namespace Conquest\Command\Commands;

use Conquest\Command\Concerns\FillsContent;
use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Filament\Support\Commands\Concerns\CanGeneratePanels;
use Filament\Support\Commands\Concerns\CanManipulateFiles;

#[AsCommand(name: 'conquest:migration')]
class ConquestMigrationMakeCommand extends Command
{
    use FillsContent;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file.';

     /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @return void
     */
    public function __construct(MigrationCreator $creator)
    {
        parent::__construct();
        $this->creator = $creator;
        $this->creator->afterCreate($this->fillContent());
    }

    public function handle(): int
    {
        // Ensure migration does not exist

        // getStub

        // getPath

        // Make directory if needed

        // Write file
        dd(TableGuesser::guess($this->getFormattedName()));



        $this->components->info(sprintf('Migration [%s] created successfully.', $this->getFormattedName()));
    }

    protected function getWritePath()
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
            ['attributes', 'a', InputOption::VALUE_OPTIONAL, 'The attributes of the migration'],
        ];
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What should the migration be named?',
                'E.g. create_users_table',
            ]
        ];
    }
}