<?php

namespace Conquest\Command\Database\Migrations;

use Illuminate\Filesystem\Filesystem;
use Conquest\Command\Concerns\FillsContent;
use Illuminate\Database\Migrations\MigrationCreator;

class ConquestMigrationCreator extends MigrationCreator
{
    use FillsContent;

    protected string $content = '';

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files, string $content = '')
    {
        $this->files = $files;
        $this->content = $content;
    }
    
    protected function getStub($table, $create): string
    {
        return $this->files->get(__DIR__.'/stubs/migration.stub');
    }

    protected function getStubPath(): string
    {
        return __DIR__.'/stubs/migration.stub';
    }

    protected function populateStub($stub, $table)
    {
        $stub = parent::populateStub($stub, $table);
        $stub = $this->fillContent($stub);
        return $stub;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}