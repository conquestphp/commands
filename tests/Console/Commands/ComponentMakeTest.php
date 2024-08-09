<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->js = resource_path('js/Components');
});

afterEach(function () {
    if (File::isDirectory($this->js)) {
        File::deleteDirectory($this->js);
    }
});

it('can create a component at root', function () {
    Artisan::call('make:js-component', [
        'name' => $p = 'Component',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('<template>');  
});

it('can create a component with nesting', function () {
    Artisan::call('make:js-component', [
        'name' => $p = 'Test/Component',
    ]);

    expect(File::exists($path = $this->js."/$p.vue"))->toBeTrue();
    expect(File::get($path))->toContain('<template>');  
});
