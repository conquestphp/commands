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
     * Get the formatted name argument.
     */
    public function getInputName(): Stringable;

    /**
     * Get the path to write the file to.
     */
    public function getWritePath(): string;

    /**
     * Get the file extension.
     */
    public function getFileExtension(): string;

    /**
     * Get the full path to the file.
     */
    public function getFilePath(string $path, string $file): string;
}
