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
     * @param  string  $content
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files, null);
    }
    
    protected function getStub($table, $create): string
    {
        return $this->files->get(__DIR__.'/stubs/conquest.migration.stub');
    }
    
    protected function populateStub($stub, $name)
    {
        $stub = $this->fillContent($stub);
        return parent::populateStub($stub, $name);
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