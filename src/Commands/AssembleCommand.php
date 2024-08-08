<?php

namespace Conquest\Assemble\Commands;

use Illuminate\Console\Command;

class AssembleCommand extends Command
{
    public $signature = 'assemble';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
