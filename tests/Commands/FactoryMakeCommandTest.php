<?php

use Conquest\Command\Enums\SchemaColumn;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->factories = app()->databasePath('factories');
});

afterEach(function () {
    collect(File::files($this->factories))
        ->each(fn ($file) => unlink($file->getPathname()));
});

it('can create a conquest factory which is default migration', function () {
    Artisan::call('make:conquest-factory', [
        'name' => $m = 'ExampleVersion',
    ]);

    $files = File::files($this->factories);
    $file = collect($files)->first(fn ($file) => str($file->getFilename())->startsWith($m));
    expect($file)->not->toBeNull();
    expect($file->getContents())->toContain($m.'Factory');
});

it('can create a conquest factory with columns', function () {
    Artisan::call('make:conquest-factory', [
        'name' => $m = 'ExampleVersion',
        '--columns' => 'name,user_id,quantity'
    ]);

    $files = File::files($this->factories);
    $file = collect($files)->first(fn ($file) => str($file->getFilename())->startsWith($m));
    expect($file)->not->toBeNull();
    expect($file->getContents())
        ->toContain($m.'Factory')
        ->toContain(SchemaColumn::Name->factory('name'))
        ->toContain(SchemaColumn::ForeignId->factory('user_id'))
        ->toContain(SchemaColumn::Quantity->factory('quantity'));
});