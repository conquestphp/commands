<?php

declare(strict_types=1);

namespace Conquest\Command\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use Illuminate\Support\Stringable;
use function Laravel\Prompts\select;
use Conquest\Command\Concerns\CanIndentStrings;
use Symfony\Component\Console\Input\InputOption;
use Conquest\Command\Concerns\InteractsWithFiles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Conquest\Command\Contracts\GeneratesBoilerplate;
use Symfony\Component\Console\Output\OutputInterface;
use Conquest\Command\Concerns\InteractsWithMigrations;

#[AsCommand(name: 'make:conquest-request', description: 'Create a new form request class with Conquest')]
class MakeRequestCommand extends Command implements GeneratesBoilerplate
{
    use InteractsWithFiles;
    use InteractsWithMigrations;
    use CanIndentStrings;

    /**
     * The type of class being generated.
     * 
     * @var string
     */
    protected $type = 'Request';
    
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

        dd($this->getWritePath(), $this->getInputName()->value());
        return self::SUCCESS;
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the request to generate.'],
            ['method', InputArgument::OPTIONAL, 'The method of the request to generate.'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the request even if it already exists'],
        ];
    }

    public function getInputName(): Stringable
    {
        return str($this->argument('name'))
            ->beforeLast('.php')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replaceLast($this->type, '')
            ->replace('/', '\\');
    }

    public function getWritePath(): string
    {
        return app_path('Http/Requests');
    }

    public function getFileExtension(): string
    {
        return '.php';
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                sprintf('What should the %s be named?', strtolower($this->type)),
                'E.g. UserIndexRequest',
            ],
        ];
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->didReceiveOptions($input)) {
            return;
        }

        // collect(multiselect('Would you like any of the following?', [
        //     'form' => 'As form',
        //     'force' => 'Force creation',
        // ]))->each(fn ($option) => $input->setOption($option, true));
    }
}