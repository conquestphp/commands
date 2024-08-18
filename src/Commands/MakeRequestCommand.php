<?php

declare(strict_types=1);

namespace Conquest\Command\Commands;

use Conquest\Command\Concerns\HasSchemaColumns;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\spin;

#[AsCommand(name: 'make:conquest-request', description: 'Create a new form request class with Conquest')]
class MakeRequestCommand extends MakeBoilerplateCommand
{
    use HasSchemaColumns;

    protected $type = 'Request';
    
    public function handle(): int
    {
        if (blank($this->argument('method')) && !confirm('You have not provided a method name, would you like to proceed?')) {
            return self::SUCCESS;
        }

        $request = $this->getInputName()->append($this->type)->value();
        $class = class_basename($request);
        $path = $this->getFilePath($this->getWritePath(), $request . $this->getFileExtension());
        $namespace = $this->getInputName()->replace('/', '\\')
            ->prepend('App\\Http\\Requests\\')
            ->beforeLast('\\')
            ->value();
        
        if (! $this->hasOption('force') && $this->checkForCollision($path)) {
            return static::INVALID;
        }

        spin(static fn () => $this->copyStubToApp('conquest.request', $path, [
            'namespace' => $namespace,
            'class' => $class,
            'authorization' => $this->getAuthorization(),
            'rules' => $this->getRules()
        ]));

        if (windows_os()) {
            $path = str_replace('/', '\\', $path);
        }


        $this->components->info(sprintf('%s [%s] created successfully.', $this->type, $path));
        
        return self::SUCCESS;
    }

    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the request even if it already exists'],
            ['gate', 'g', InputOption::VALUE_NONE, 'Resolve a policy for the request, using the root of the class name'],
            ['columns', 'c', InputOption::VALUE_REQUIRED, 'Generate rules for the request using the property list'],
            ['suppress', 's', InputOption::VALUE_NONE, 'Suppress the creation of the request'],
        ];
    }

    public function getWritePath(): string
    {
        return app_path('Http/Requests');
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

    private function getAuthorization(): string
    {
        if (!$this->option('gate')) {
            return 'true';
        }

        return 'false';
    }

    private function getRules(): string
    {
        if (!$this->option('properties')) {
            return '//';
        }

        return '[]';
    }
}