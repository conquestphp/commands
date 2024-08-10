<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->js = resource_path('js/Pages');
});

afterEach(function () {
    if (File::isDirectory($this->js)) {
        File::deleteDirectory($this->js);
    }
});

it('can create a page at root', function () {
    Artisan::call('make:page', [
        'name' => $p = 'TestIndex',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('PageLayout');
});

it('can create a page with nesting', function () {
    Artisan::call('make:page', [
        'name' => $p = 'Test/TestIndex',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('PageLayout');
});

it('can be made into a form page', function () {
    Artisan::call('make:page', [
        'name' => $p = 'TestIndex',
        '--form' => true,
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const form');
});

it('can be force written', function () {
    Artisan::call('make:page', [
        'name' => $p = 'TestIndex',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->not->toContain('const form');

    Artisan::call('make:page', [
        'name' => $p = 'TestIndex',
        '--force' => true,
        '--form' => true,
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const form');
});
