<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->migrations = database_path('migrations');
});

afterEach(function () {
    // Only delete files inside migrations folder starting with 2024
    $files = File::files($this->migrations);
    $files = collect($files)->filter(fn ($file) => str($file->getFilename())->startsWith('2024'));
    $files->each(fn ($file) => unlink($file->getPathname()));
});

it('can create a conquest migration which is default migration', function () {
    Artisan::call('make:conquest-migration', [
        'name' => $m = 'User',
    ]);

    $files = File::files($this->migrations);
    $file = collect($files)->first(fn ($file) => str($file->getFilename())->startsWith('2024'));
    expect($file)->not->toBeNull();
    expect($file->getFilename())->toContain(str($m)->snake());
    dd($file->getContents());
});