<?php

use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

afterEach(function () {
    // Clean up created files after each test
    File::deleteDirectory(app()->basePath('app/Http'));
});

it('can create a conquest endpoint which is not bound to a method', function () {
    artisan('make:conquest', [
        'name' => 'Test',
        '--force' => true,
    ]);

    $controller = app()->basePath('app/Http/Controllers/TestController.php');
    $request = app()->basePath('app/Http/Requests/TestRequest.php');
    expect(File::exists($controller))->toBeTrue();
    expect(File::exists($request))->toBeTrue();
});