<?php

use Conquest\Command\Enums\SchemaColumn;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->migrations = app()->basePath('migrations');
});

afterEach(function () {
    // Only delete files inside migrations folder starting with 2024
    $files = File::files($this->migrations);
    $files = collect($files)->filter(fn ($file) => str($file->getFilename())->startsWith('2024'));
    $files->each(fn ($file) => unlink($file->getPathname()));
});

it('can create a conquest migration which is default migration', function () {
    Artisan::call('conquest:migration', [
        'name' => $m = 'ExampleVersion',
    ]);

    $files = File::files($this->migrations);
    $file = collect($files)->first(fn ($file) => str($file->getFilename())->startsWith('2024'));
    expect($file)->not->toBeNull();
    expect($file->getFilename())->toContain(str($m)->snake());
    expect($file->getContents())->toContain(str($m)->snake());
});

it('can create a conquest migration with attributes', function () {
    Artisan::call('conquest:migration', [
        'name' => $m = 'ExampleVersion',
        '--attributes' => 'name,user_id,quantity'
    ]);

    $files = File::files($this->migrations);
    $file = collect($files)->first(fn ($file) => str($file->getFilename())->startsWith('2024'));
    expect($file)->not->toBeNull();
    expect($file->getFilename())->toContain(str($m)->snake());
    expect($file)->getContents()
        ->toContain(str($m)->snake())
        ->toContain(SchemaColumn::Name->blueprint('name'))
        ->toContain(SchemaColumn::ForeignId->blueprint('user_id'));    
});