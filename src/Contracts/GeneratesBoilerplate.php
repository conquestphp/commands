<?php

namespace Conquest\Command\Contracts;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Stringable;

interface GeneratesBoilerplate extends PromptsForMissingInput
{
    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): int;

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Get the formatted name argument.
     * 
     * @return Stringable
     */
    public function getInputName(): Stringable;

    /**
     * Get the path to write the file to.
     * 
     * @return string
     */
    public function getWritePath(): string;

    /**
     * Get the file extension.
     * 
     * @return string
     */
    public function getFileExtension(): string;
}

