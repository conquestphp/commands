<?php

namespace Conquest\Assemble\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Conquest\Assemble\Assemble
 */
class Assemble extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Conquest\Assemble\Assemble::class;
    }
}
