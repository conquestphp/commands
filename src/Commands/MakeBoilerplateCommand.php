<?php

declare(strict_types=1);

namespace Conquest\Command\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Stringable;
use Conquest\Command\Concerns\CanIndentStrings;
use Conquest\Command\Concerns\InteractsWithFiles;
use Symfony\Component\Console\Input\InputArgument;
use Conquest\Command\Contracts\GeneratesBoilerplate;
use Conquest\Command\Concerns\InteractsWithMigrations;

abstract class MakeBoilerplateCommand extends Command implements GeneratesBoilerplate
{
    use InteractsWithFiles;
    use InteractsWithMigrations;
    use CanIndentStrings;

    /**
     * The type of class being generated.
     * 
     * @var string
     */
    protected $type;
    
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, sprintf('The name of the %s to generate.', strtolower($this->type))],
            ['method', InputArgument::OPTIONAL, sprintf('The method of the %s to use.', strtolower($this->type))],
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

    public function getFileExtension(): string
    {
        return '.php';
    }

    public function getFilePath(string $path, string $file): string
    {
        return str($path)
            ->rtrim('/')
            
            ->append(str($file)
                ->ltrim('/')
                ->prepend('/')
            )->replace('//', '/')
            ->replace('\\', '/')
            ->value();
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                sprintf('What should the %s be named?', strtolower($this->type)),
                sprintf('E.g. User%s%s', in_array('method', array_column($this->getArguments(), 0)) ? 'Show' : '', $this->type),
            ],
        ];
    }
}