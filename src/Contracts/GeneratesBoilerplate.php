<?php

namespace Conquest\Command\Contracts;

use Illuminate\Contracts\Console\PromptsForMissingInput;

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
     * @return string
     */
    public function getName(): string;
}

