<?php

namespace Filament\Commands;

use Filament\Panel;
use Illuminate\Support\Arr;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use Conquest\Command\Concerns\InteractsWithFiles;
use Symfony\Component\Console\Attribute\AsCommand;
use Conquest\Command\Contracts\GeneratesBoilerplate;
use Conquest\Command\Concerns\InteractsWithMigrations;
use Filament\Forms\Commands\Concerns\CanGenerateForms;
use Filament\Support\Commands\Concerns\CanIndentStrings;
use Filament\Tables\Commands\Concerns\CanGenerateTables;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanReadModelSchemas;

#[AsCommand(name: 'make:conquest-request', description: 'Create a new form request class with Conquest')]
class MakeResourceCommand extends Command implements GeneratesBoilerplate
{
    use InteractsWithFiles;
    use InteractsWithMigrations;
    
    public function handle(): int
    {
        // $model = (string) str($this->argument('name') ?? text(
        //     label: 'What is the model name?',
        //     placeholder: 'BlogPost',
        //     required: true,
        // ))
        //     ->studly()
        //     ->beforeLast('Resource')
        //     ->trim('/')
        //     ->trim('\\')
        //     ->trim(' ')
        //     ->studly()
        //     ->replace('/', '\\');

        // if (blank($model)) {
        //     $model = 'Resource';
        // }

        // $modelNamespace = $this->option('model-namespace') ?? 'App\\Models';

        // $baseResourcePath =
        //     (string) str($resource)
        //         ->prepend('/')
        //         ->prepend($path)
        //         ->replace('\\', '/')
        //         ->replace('//', '/');

        // $resourcePath = "{$baseResourcePath}.php";
        // $resourcePagesDirectory = "{$baseResourcePath}/Pages";
        // $listResourcePagePath = "{$resourcePagesDirectory}/{$listResourcePageClass}.php";
        // $manageResourcePagePath = "{$resourcePagesDirectory}/{$manageResourcePageClass}.php";
        // $createResourcePagePath = "{$resourcePagesDirectory}/{$createResourcePageClass}.php";
        // $editResourcePagePath = "{$resourcePagesDirectory}/{$editResourcePageClass}.php";
        // $viewResourcePagePath = "{$resourcePagesDirectory}/{$viewResourcePageClass}.php";


        // $this->components->info("Filament resource [{$resourcePath}] created successfully.");

        return self::SUCCESS;
    }
}