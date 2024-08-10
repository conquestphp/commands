<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->js = resource_path('js/Modals');
});

afterEach(function () {
    if (File::isDirectory($this->js)) {
        File::deleteDirectory($this->js);
    }
});

it('can create a modal at root', function () {
    Artisan::call('make:modal', [
        'name' => $p = 'TestShow',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const { close } = useModal()');  
});

it('can create a modal with nesting', function () {
    Artisan::call('make:modal', [
        'name' => $p = 'Test/TestShow',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const { close } = useModal()');  
});

it('can be made into a form modal', function () {
    Artisan::call('make:modal', [
        'name' => $p = 'TestShow',
        '--form' => true,
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const form');  
});

it('can be force written', function () {
    Artisan::call('make:modal', [
        'name' => $p = 'TestShow',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->not->toContain('const form');


    Artisan::call('make:modal', [
        'name' => $p = 'TestShow',
        '--force' => true,
        '--form' => true,
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('const form');
});
